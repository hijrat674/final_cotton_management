<?php

namespace App\Http\Requests\Sales;

use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSalePaymentRequest extends FormRequest
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
            'sale_id' => ['required', 'exists:sales,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50', 'in:'.implode(',', array_keys(SalePayment::paymentMethodOptions()))],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Sale|null $sale */
            $sale = Sale::query()->with('customer')->find($this->integer('sale_id'));

            if (! $sale) {
                return;
            }

            if ($sale->customer_id !== $this->integer('customer_id')) {
                $validator->errors()->add('customer_id', 'The selected customer does not match the sale.');
            }

            if ((float) $this->input('amount', 0) > (float) $sale->remaining_amount) {
                $validator->errors()->add('amount', 'Payment amount cannot exceed the remaining balance.');
            }
        });
    }
}
