<?php

namespace App\Http\Requests\Inventory;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreInventoryTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_PRODUCTION) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'quantity_in' => $this->filled('quantity_in') ? $this->input('quantity_in') : null,
            'quantity_out' => $this->filled('quantity_out') ? $this->input('quantity_out') : null,
            'reference_id' => $this->filled('reference_id') ? $this->input('reference_id') : null,
            'reference_type' => $this->filled('reference_type') ? $this->input('reference_type') : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'transaction_type' => ['required', 'string', 'max:100', Rule::in(array_keys(InventoryTransaction::transactionTypeOptions()))],
            'reference_type' => ['nullable', 'string', 'max:100', Rule::in(array_keys(InventoryTransaction::referenceTypeOptions()))],
            'reference_id' => ['nullable', 'integer'],
            'quantity_in' => ['required_without:quantity_out', 'nullable', 'numeric', 'min:0'],
            'quantity_out' => ['required_without:quantity_in', 'nullable', 'numeric', 'min:0'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $quantityIn = (float) ($this->input('quantity_in') ?? 0);
            $quantityOut = (float) ($this->input('quantity_out') ?? 0);

            if ($quantityIn <= 0 && $quantityOut <= 0) {
                $validator->errors()->add('quantity_in', 'Enter a quantity in or quantity out value greater than zero.');
            }

            if ($quantityIn > 0 && $quantityOut > 0) {
                $validator->errors()->add('quantity_in', 'Quantity in and quantity out cannot both be greater than zero.');
            }

            if ($quantityOut > 0) {
                $item = InventoryItem::query()
                    ->withStockSummary()
                    ->find($this->input('inventory_item_id'));

                if ($item && $quantityOut > $item->current_stock) {
                    $validator->errors()->add(
                        'quantity_out',
                        'Stock out quantity cannot exceed the available stock of '.$item->current_stock.' '.$item->unit.'.'
                    );
                }
            }
        });
    }
}
