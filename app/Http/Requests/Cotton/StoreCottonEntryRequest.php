<?php

namespace App\Http\Requests\Cotton;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreCottonEntryRequest extends FormRequest
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
        $grossWeight = (float) ($this->input('gross_weight') ?? 0);
        $tareWeight = (float) ($this->input('tare_weight') ?? 0);

        $this->merge([
            'net_weight' => max(0, round($grossWeight - $tareWeight, 3)),
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
            'truck_number' => ['required', 'string', 'max:255'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:20'],
            'gross_weight' => ['required', 'numeric', 'min:0', 'gte:tare_weight'],
            'tare_weight' => ['required', 'numeric', 'min:0', 'lte:gross_weight'],
            'entry_date' => ['required', 'date'],
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
