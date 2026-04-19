<?php

namespace App\Http\Requests\Production;

use App\Models\Employee;
use App\Models\ProductionStageOutput;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProductionStageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole(User::ROLE_ADMIN) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $outputs = collect($this->input('outputs', []))
            ->filter(fn (mixed $output) => is_array($output))
            ->map(function (array $output): array {
                return [
                    'inventory_item_id' => $output['inventory_item_id'] ?? null,
                    'output_type' => $output['output_type'] ?? null,
                    'quantity' => $output['quantity'] ?? null,
                    'unit' => $output['unit'] ?? null,
                ];
            })
            ->values()
            ->all();

        $this->merge(['outputs' => $outputs]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stage_name' => ['required', 'string', 'max:255'],
            'source_inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'input_quantity' => ['required', 'numeric', 'min:1'],
            'stage_date' => ['required', 'date'],
            'handled_by_employee_id' => ['required', 'exists:employees,id'],
            'notes' => ['nullable', 'string'],
            'total_cost' => ['required', 'numeric', 'min:0.01'],
            'outputs' => ['required', 'array', 'min:1'],
            'outputs.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'outputs.*.output_type' => ['required', 'in:'.implode(',', array_keys(ProductionStageOutput::outputTypeOptions()))],
            'outputs.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'outputs.*.unit' => ['required', 'string', 'max:20'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $employeeId = $this->integer('handled_by_employee_id');

            if ($employeeId > 0 && ! Employee::query()->active()->whereKey($employeeId)->exists()) {
                $validator->errors()->add('handled_by_employee_id', 'Only active employees can be assigned to production stages.');
            }

            $totalOutput = collect($this->input('outputs', []))
                ->sum(fn (array $output) => (float) ($output['quantity'] ?? 0));

            if ($totalOutput <= 0) {
                $validator->errors()->add('outputs', 'The combined output quantity must be greater than zero.');
            }
        });
    }
}
