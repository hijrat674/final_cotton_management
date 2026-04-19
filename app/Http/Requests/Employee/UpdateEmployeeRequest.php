<?php

namespace App\Http\Requests\Employee;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
        /** @var Employee $employee */
        $employee = $this->route('employee');

        return [
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('employees', 'user_id')->ignore($employee->id),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'position' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'hire_date' => ['required', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(array_keys(Employee::statusOptions()))],
        ];
    }
}
