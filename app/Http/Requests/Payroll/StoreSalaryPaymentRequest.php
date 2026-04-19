<?php

namespace App\Http\Requests\Payroll;

use App\Models\SalaryPayment;
use App\Models\SalaryRecord;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSalaryPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ADMIN) ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'salary_record_id' => ['required', 'exists:salary_records,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50', 'in:'.implode(',', array_keys(SalaryPayment::paymentMethodOptions()))],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $salaryRecord = SalaryRecord::query()->find($this->integer('salary_record_id'));

            if (! $salaryRecord) {
                return;
            }

            if ((int) $salaryRecord->employee_id !== $this->integer('employee_id')) {
                $validator->errors()->add('employee_id', 'The selected employee does not match the salary record.');
            }

            if ((float) $this->input('amount', 0) > (float) $salaryRecord->remaining_amount) {
                $validator->errors()->add('amount', 'Payment amount cannot exceed the remaining salary balance.');
            }
        });
    }
}
