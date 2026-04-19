<?php

namespace App\Http\Requests\Accounting;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AccountingFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_MANAGER) ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'account_type' => ['nullable', 'string', 'in:'.implode(',', array_keys(\App\Models\Account::typeOptions()))],
            'reference_type' => ['nullable', 'string', 'in:'.implode(',', array_keys(\App\Models\JournalEntry::referenceTypeOptions()))],
            'account_id' => ['nullable', 'exists:accounts,id'],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
