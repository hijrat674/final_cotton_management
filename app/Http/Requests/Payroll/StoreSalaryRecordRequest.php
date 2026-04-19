<?php

namespace App\Http\Requests\Payroll;

use App\Models\EmployeeAdvance;
use App\Models\SalaryRecord;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSalaryRecordRequest extends FormRequest
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
            'employee_id' => ['required', 'exists:employees,id'],
            'salary_month' => ['required', 'integer', 'between:1,12'],
            'salary_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'deduction' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'bonus' => $this->input('bonus', 0),
            'deduction' => $this->input('deduction', 0),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $exists = SalaryRecord::query()
                ->where('employee_id', $this->integer('employee_id'))
                ->where('salary_month', $this->integer('salary_month'))
                ->where('salary_year', $this->integer('salary_year'))
                ->exists();

            if ($exists) {
                $validator->errors()->add('employee_id', 'A salary record already exists for this employee and payroll period.');
            }

            $basicSalary = (float) $this->input('basic_salary', 0);
            $bonus = (float) $this->input('bonus', 0);
            $deduction = (float) $this->input('deduction', 0);
            $grossSalary = round($basicSalary + $bonus - $deduction, 2);

            if ($grossSalary < 0) {
                $validator->errors()->add('deduction', 'Deductions cannot reduce total salary below zero.');
            }

            $pendingAdvances = round((float) EmployeeAdvance::query()
                ->where('employee_id', $this->integer('employee_id'))
                ->where('status', EmployeeAdvance::STATUS_PENDING)
                ->sum('amount'), 2);

            if ($grossSalary - $pendingAdvances < 0) {
                $validator->errors()->add('employee_id', 'Pending advances exceed the gross salary for this period. Clear or adjust the advances before generating payroll.');
            }
        });
    }
}
