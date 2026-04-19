<?php

namespace App\Http\Requests\Sales;

use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class UpdateSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_SALES) ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'sale_date' => ['required', 'date'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Sale $sale */
            $sale = $this->route('sale');
            $sale->loadMissing('items');

            $items = collect($this->input('items', []))
                ->filter(fn (mixed $item) => is_array($item) && ! empty($item['inventory_item_id']));

            if ($items->isEmpty()) {
                $validator->errors()->add('items', 'At least one sale item is required.');

                return;
            }

            $inventoryItems = InventoryItem::query()
                ->withStockSummary()
                ->whereIn('id', $items->pluck('inventory_item_id')->unique())
                ->get()
                ->keyBy('id');

            $existingQuantities = $sale->items
                ->groupBy('inventory_item_id')
                ->map(fn (Collection $group) => round((float) $group->sum('quantity'), 3));

            $soldQuantities = [];

            foreach ($items as $index => $item) {
                $inventoryItem = $inventoryItems->get((int) $item['inventory_item_id']);

                if (! $inventoryItem) {
                    $validator->errors()->add("items.$index.inventory_item_id", 'The selected inventory item could not be found.');

                    continue;
                }

                $quantity = round((float) $item['quantity'], 3);
                $soldQuantities[$inventoryItem->id] = ($soldQuantities[$inventoryItem->id] ?? 0) + $quantity;
                $availableStock = round($inventoryItem->current_stock + (float) ($existingQuantities[$inventoryItem->id] ?? 0), 3);

                if ($soldQuantities[$inventoryItem->id] > $availableStock) {
                    $validator->errors()->add(
                        "items.$index.quantity",
                        'Sold quantity exceeds the available stock for '.$inventoryItem->product_name.'.'
                    );
                }
            }

            $totalAmount = round($items->sum(
                fn (array $item) => round((float) $item['quantity'], 3) * round((float) $item['unit_price'], 2)
            ), 2);

            if ((float) $this->input('paid_amount', 0) > $totalAmount) {
                $validator->errors()->add('paid_amount', 'Paid amount cannot exceed the invoice total.');
            }

            if ($sale->payments()->exists() && (float) $sale->paid_amount !== (float) $this->input('paid_amount')) {
                $validator->errors()->add('paid_amount', 'Paid amount cannot be edited after follow-up payments have been recorded.');
            }
        });
    }
}
