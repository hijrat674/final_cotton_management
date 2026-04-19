@php
    $stage = $stage ?? null;
    $selectedSourceItemId = old('source_inventory_item_id', $stage->source_inventory_item_id ?? '');
    $linkedProductionExpense = isset($stage)
        ? $stage->expenses->first(fn ($expense) => $expense->expenseType?->name === \App\Models\ExpenseType::NAME_PRODUCTION)
        : null;
    $expenseAmount = old('total_cost', isset($stage) ? number_format((float) ($linkedProductionExpense->amount ?? 0), 2, '.', '') : '0.00');
    $outputRows = old('outputs');

    if ($outputRows === null && isset($stage)) {
        $outputRows = $stage->outputs->map(fn ($output) => [
            'inventory_item_id' => $output->inventory_item_id,
            'output_type' => $output->output_type,
            'quantity' => number_format((float) $output->quantity, 3, '.', ''),
            'unit' => $output->unit,
        ])->all();
    }

    if (empty($outputRows)) {
        $outputRows = [[
            'inventory_item_id' => '',
            'output_type' => \App\Models\ProductionStageOutput::TYPE_MAIN_OUTPUT,
            'quantity' => '',
            'unit' => '',
        ]];
    }
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="form-section-card">
            <div class="form-section-header">
                <h3>{{ __('production.form.stage_setup.title') }}</h3>
                <p>{{ __('production.form.stage_setup.text') }}</p>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="stage_name" class="form-label">{{ __('production.fields.stage_name') }}</label>
                    <input type="text" id="stage_name" name="stage_name" value="{{ old('stage_name', $stage->stage_name ?? '') }}" class="form-control @error('stage_name') is-invalid @enderror" required>
                    @error('stage_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="stage_date" class="form-label">{{ __('production.fields.stage_date') }}</label>
                    <input type="date" id="stage_date" name="stage_date" value="{{ old('stage_date', isset($stage) ? $stage->stage_date->toDateString() : now()->toDateString()) }}" class="form-control @error('stage_date') is-invalid @enderror" required>
                    @error('stage_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-7">
                    <label for="source_inventory_item_id" class="form-label">{{ __('production.fields.source_material') }}</label>
                    <select id="source_inventory_item_id" name="source_inventory_item_id" class="form-select @error('source_inventory_item_id') is-invalid @enderror" required data-production-source>
                        <option value="">{{ __('production.form.select_source_material') }}</option>
                        @foreach($inventoryItems as $item)
                            <option
                                value="{{ $item->id }}"
                                data-stock="{{ number_format((float) $item->current_stock, 3, '.', '') }}"
                                data-unit="{{ $item->unit }}"
                                @selected((string) $selectedSourceItemId === (string) $item->id)
                            >
                                {{ $item->product_name }} ({{ number_format((float) $item->current_stock, 3) }} {{ strtoupper($item->unit) }})
                            </option>
                        @endforeach
                    </select>
                    @error('source_inventory_item_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-5">
                    <label for="input_quantity" class="form-label">{{ __('production.fields.input_quantity') }}</label>
                    <input type="number" step="0.001" min="1" id="input_quantity" name="input_quantity" value="{{ old('input_quantity', isset($stage) ? number_format((float) $stage->input_quantity, 3, '.', '') : '') }}" class="form-control @error('input_quantity') is-invalid @enderror" required data-production-input>
                    @error('input_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="stock-insight mt-3" id="productionStockHint">
                {{ __('production.form.stock_hint') }}
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label for="total_cost" class="form-label">{{ __('production.fields.total_cost') }}</label>
                    <input type="number" step="0.01" min="0.01" id="total_cost" name="total_cost" value="{{ $expenseAmount }}" class="form-control @error('total_cost') is-invalid @enderror" required>
                    <div class="form-text">{{ __('production.form.total_cost_help') }}</div>
                    @error('total_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="handled_by_employee_id" class="form-label">{{ __('production.fields.handled_by') }}</label>
                    <select id="handled_by_employee_id" name="handled_by_employee_id" class="form-select @error('handled_by_employee_id') is-invalid @enderror" required>
                        <option value="">{{ __('production.form.select_employee') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected((string) old('handled_by_employee_id', $stage->handled_by_employee_id ?? '') === (string) $employee->id)>
                                {{ $employee->full_name }} - {{ $employee->position }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">{{ __('production.form.handled_by_help') }}</div>
                    @error('handled_by_employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <label for="notes" class="form-label">{{ __('production.fields.notes') }}</label>
                <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $stage->notes ?? '') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="form-section-card highlight-card">
            <div class="form-section-header">
                <h3>{{ __('production.form.posting_rules.title') }}</h3>
                <p>{{ __('production.form.posting_rules.text') }}</p>
            </div>

            <ul class="rules-list mb-0">
                <li>{{ __('production.form.posting_rules.source_stock') }}</li>
                <li>{{ __('production.form.posting_rules.output_required') }}</li>
                <li>{{ __('production.form.posting_rules.output_units') }}</li>
                <li>{{ __('production.form.posting_rules.stock_flow') }}</li>
                <li>{{ __('production.form.posting_rules.cost_record') }}</li>
            </ul>
        </div>
    </div>
</div>

<div class="form-section-card mt-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
        <div class="form-section-header mb-0">
            <h3>{{ __('production.form.outputs.title') }}</h3>
            <p>{{ __('production.form.outputs.text') }}</p>
        </div>
        <button type="button" class="btn btn-outline-primary" data-add-output-row>
            <i class="bi bi-plus-circle me-1"></i>
            {{ __('production.actions.add_output_row') }}
        </button>
    </div>

    @error('outputs')
        <div class="alert alert-danger alert-modern">{{ $message }}</div>
    @enderror

    <div class="output-rows" data-output-rows>
        @foreach($outputRows as $index => $output)
            <div class="output-row-card" data-output-row>
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label">{{ __('production.fields.output_item') }}</label>
                        <select name="outputs[{{ $index }}][inventory_item_id]" class="form-select @error("outputs.$index.inventory_item_id") is-invalid @enderror" required data-output-item>
                            <option value="">{{ __('production.form.select_output_item') }}</option>
                            @foreach($inventoryItems as $item)
                                <option
                                    value="{{ $item->id }}"
                                    data-unit="{{ $item->unit }}"
                                    @selected((string) ($output['inventory_item_id'] ?? '') === (string) $item->id)
                                >
                                    {{ $item->product_name }} ({{ strtoupper($item->unit) }})
                                </option>
                            @endforeach
                        </select>
                        @error("outputs.$index.inventory_item_id")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">{{ __('production.fields.output_type') }}</label>
                        <select name="outputs[{{ $index }}][output_type]" class="form-select @error("outputs.$index.output_type") is-invalid @enderror" required>
                            @foreach($outputTypeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(($output['output_type'] ?? '') === $value)>{{ __('production.output_types.' . $value) }}</option>
                            @endforeach
                        </select>
                        @error("outputs.$index.output_type")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label">{{ __('production.fields.quantity') }}</label>
                        <input type="number" step="0.001" min="0.01" name="outputs[{{ $index }}][quantity]" value="{{ $output['quantity'] ?? '' }}" class="form-control @error("outputs.$index.quantity") is-invalid @enderror" required data-output-quantity>
                        @error("outputs.$index.quantity")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-2">
                        <label class="form-label">{{ __('production.fields.unit') }}</label>
                        <input type="text" name="outputs[{{ $index }}][unit]" value="{{ $output['unit'] ?? '' }}" class="form-control @error("outputs.$index.unit") is-invalid @enderror" readonly required data-output-unit>
                        @error("outputs.$index.unit")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-1">
                        <button type="button" class="btn btn-outline-danger w-100" data-remove-output-row aria-label="{{ __('production.actions.remove_output_row') }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <template data-output-row-template>
        <div class="output-row-card" data-output-row>
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label">{{ __('production.fields.output_item') }}</label>
                    <select name="__NAME__" class="form-select" required data-output-item>
                        <option value="">{{ __('production.form.select_output_item') }}</option>
                        @foreach($inventoryItems as $item)
                            <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">
                                {{ $item->product_name }} ({{ strtoupper($item->unit) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3">
                    <label class="form-label">{{ __('production.fields.output_type') }}</label>
                    <select name="__TYPE_NAME__" class="form-select" required>
                        @foreach($outputTypeOptions as $value => $label)
                            <option value="{{ $value }}">{{ __('production.output_types.' . $value) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">{{ __('production.fields.quantity') }}</label>
                    <input type="number" step="0.001" min="0.01" name="__QUANTITY_NAME__" class="form-control" required data-output-quantity>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">{{ __('production.fields.unit') }}</label>
                    <input type="text" name="__UNIT_NAME__" class="form-control" readonly required data-output-unit>
                </div>

                <div class="col-lg-1">
                    <button type="button" class="btn btn-outline-danger w-100" data-remove-output-row aria-label="{{ __('production.actions.remove_output_row') }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <div class="output-summary-bar mt-3">
        <span>{{ __('production.form.outputs.total_output_quantity') }}</span>
        <strong id="productionOutputTotal">0.000</strong>
    </div>
</div>
