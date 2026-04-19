<div class="row g-4" data-payroll-form>
    <div class="col-xl-8">
        <div class="payroll-form-card">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select id="employee_id" name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required data-payroll-employee>
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" data-salary="{{ number_format((float) $employee->salary, 2, '.', '') }}" data-pending-advance="{{ number_format((float) ($employee->pending_advances_total ?? 0), 2, '.', '') }}" @selected((string) old('employee_id') === (string) $employee->id)>
                                {{ $employee->full_name }} - {{ $employee->department }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label for="salary_month" class="form-label">Month</label>
                    <select id="salary_month" name="salary_month" class="form-select @error('salary_month') is-invalid @enderror" required>
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" @selected((int) old('salary_month', now()->month) === (int) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('salary_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label for="salary_year" class="form-label">Year</label>
                    <select id="salary_year" name="salary_year" class="form-select @error('salary_year') is-invalid @enderror" required>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected((int) old('salary_year', now()->year) === (int) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    @error('salary_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="basic_salary" class="form-label">Basic Salary</label>
                    <input type="number" step="0.01" min="0" id="basic_salary" name="basic_salary" value="{{ old('basic_salary') }}" class="form-control @error('basic_salary') is-invalid @enderror" required data-payroll-basic>
                    @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="bonus" class="form-label">Bonus</label>
                    <input type="number" step="0.01" min="0" id="bonus" name="bonus" value="{{ old('bonus', '0.00') }}" class="form-control @error('bonus') is-invalid @enderror" data-payroll-bonus>
                    @error('bonus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="deduction" class="form-label">Deduction</label>
                    <input type="number" step="0.01" min="0" id="deduction" name="deduction" value="{{ old('deduction', '0.00') }}" class="form-control @error('deduction') is-invalid @enderror" data-payroll-deduction>
                    @error('deduction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="payroll-form-card payroll-summary-card">
            <h3 class="section-title mb-3">Salary Summary</h3>
            <div class="summary-breakdown">
                <div><span>Basic Salary</span><strong data-payroll-basic-preview>0.00</strong></div>
                <div><span>Bonus</span><strong data-payroll-bonus-preview>0.00</strong></div>
                <div><span>Deduction</span><strong data-payroll-deduction-preview>0.00</strong></div>
                <div><span>Advance Deduction</span><strong data-payroll-advance-preview>0.00</strong></div>
                <div><span>Gross Salary</span><strong data-payroll-gross-preview>0.00</strong></div>
                <div><span>Net Payable</span><strong data-payroll-total>0.00</strong></div>
            </div>
        </div>
    </div>
</div>
