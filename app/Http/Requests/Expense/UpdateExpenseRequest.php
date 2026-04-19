<?php

namespace App\Http\Requests\Expense;

use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'title' => ['required', 'string', 'max:255'],
            'expense_type_id' => ['required', 'exists:expense_types,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'production_stage_id' => ['nullable', 'exists:production_stages,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $productionTypeId = ExpenseType::query()
                ->where('name', ExpenseType::NAME_PRODUCTION)
                ->value('id');

            if (! $productionTypeId) {
                $validator->errors()->add('expense_type_id', 'The production expense type is not available. Please seed the expense types first.');

                return;
            }

            if ((int) $this->input('expense_type_id') === (int) $productionTypeId && ! $this->filled('production_stage_id')) {
                $validator->errors()->add('production_stage_id', 'A production expense must be linked to a production stage.');
            }

            if (
                (int) $this->input('expense_type_id') === (int) $productionTypeId
                && $this->filled('production_stage_id')
                && Expense::query()
                    ->where('expense_type_id', $productionTypeId)
                    ->where('production_stage_id', $this->integer('production_stage_id'))
                    ->whereKeyNot($this->route('expense')?->id)
                    ->exists()
            ) {
                $validator->errors()->add('production_stage_id', 'A production cost already exists for the selected production stage.');
            }
        });
    }
}
