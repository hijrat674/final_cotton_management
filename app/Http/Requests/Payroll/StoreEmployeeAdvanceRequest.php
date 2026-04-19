<?php

namespace App\Http\Requests\Payroll;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeAdvanceRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'advance_date' => ['required', 'date'],
            'reason' => ['nullable', 'string'],
        ];
    }
}
