@php
    $expense = $expense ?? null;
    $selectedProductionStageId = old('production_stage_id', $expense->production_stage_id ?? $selectedProductionStageId ?? '');
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="expense-form-card">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">{{ __('expenses.fields.title') }}</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $expense->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="expense_type_id" class="form-label">{{ __('expenses.fields.type') }}</label>
                    <select id="expense_type_id" name="expense_type_id" class="form-select @error('expense_type_id') is-invalid @enderror" required>
                        <option value="">{{ __('expenses.form.select_type') }}</option>
                        @foreach($expenseTypes as $expenseType)
                            @php($translationKey = 'expenses.types.' . $expenseType->name)
                            <option value="{{ $expenseType->id }}" @selected((string) old('expense_type_id', $expense->expense_type_id ?? '') === (string) $expenseType->id)>
                                {{ \Illuminate\Support\Facades\Lang::has($translationKey) ? __($translationKey) : (\App\Models\ExpenseType::defaultOptions()[$expenseType->name] ?? ucfirst($expenseType->name)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="amount" class="form-label">{{ __('expenses.fields.amount') }}</label>
                    <input type="number" step="0.01" min="0.01" id="amount" name="amount" value="{{ old('amount', isset($expense) ? number_format((float) $expense->amount, 2, '.', '') : '') }}" class="form-control @error('amount') is-invalid @enderror" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="expense_date" class="form-label">{{ __('expenses.fields.date') }}</label>
                    <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date', isset($expense) ? $expense->expense_date->toDateString() : now()->toDateString()) }}" class="form-control @error('expense_date') is-invalid @enderror" required>
                    @error('expense_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label for="production_stage_id" class="form-label">{{ __('expenses.fields.linked_stage') }}</label>
                    <select id="production_stage_id" name="production_stage_id" class="form-select @error('production_stage_id') is-invalid @enderror">
                        <option value="">{{ __('expenses.form.general_expense') }}</option>
                        @foreach($productionStages as $stage)
                            <option value="{{ $stage->id }}" @selected((string) $selectedProductionStageId === (string) $stage->id)>
                                {{ $stage->stage_name }} - {{ $stage->stage_date->translatedFormat('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">{{ __('expenses.form.link_help') }}</div>
                    @error('production_stage_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">{{ __('expenses.fields.note') }}</label>
                    <textarea id="notes" name="notes" rows="5" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $expense->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="expense-form-card expense-form-help">
            <h3 class="section-title mb-3">{{ __('expenses.form.guidance_title') }}</h3>
            <ul class="expense-guidance-list mb-0">
                <li>{{ __('expenses.form.guidance_1') }}</li>
                <li>{{ __('expenses.form.guidance_2') }}</li>
                <li>{{ __('expenses.form.guidance_3') }}</li>
                <li>{{ __('expenses.form.guidance_4') }}</li>
            </ul>
        </div>
    </div>
</div>
