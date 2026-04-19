<?php

namespace App\Http\Requests\Inventory;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ADMIN) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var \App\Models\InventoryItem $inventoryItem */
        $inventoryItem = $this->route('inventory_item');

        return [
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('inventory_items', 'product_code')->ignore($inventoryItem->id),
            ],
            'product_type' => ['required', Rule::in(array_keys(InventoryItem::productTypeOptions()))],
            'unit' => ['required', Rule::in(array_keys(InventoryItem::unitOptions()))],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
