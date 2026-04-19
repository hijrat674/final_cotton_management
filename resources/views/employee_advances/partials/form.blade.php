<div class="row g-4" data-advance-form>
    <div class="col-xl-8">
        <div class="payroll-form-card">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select id="employee_id" name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required data-advance-employee>
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" data-pending-advance="{{ number_format((float) ($employee->pending_advances_total ?? 0), 2, '.', '') }}" @selected((string) old('employee_id') === (string) $employee->id)>
                                {{ $employee->full_name }} - {{ $employee->department }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="advance_date" class="form-label">Advance Date</label>
                    <input type="date" id="advance_date" name="advance_date" value="{{ old('advance_date', now()->toDateString()) }}" class="form-control @error('advance_date') is-invalid @enderror" required>
                    @error('advance_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="amount" class="form-label">Advance Amount</label>
                    <input type="number" step="0.01" min="0.01" id="amount" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required data-advance-amount>
                    @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="reason" class="form-label">Reason</label>
                    <textarea id="reason" name="reason" rows="4" class="form-control @error('reason') is-invalid @enderror">{{ old('reason') }}</textarea>
                    @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="payroll-form-card payroll-summary-card">
            <h3 class="section-title mb-3">Advance Summary</h3>
            <div class="summary-breakdown">
                <div><span>Selected Advance</span><strong data-advance-preview>{{ number_format((float) old('amount', 0), 2) }}</strong></div>
                <div><span>Existing Pending</span><strong data-advance-pending-preview>0.00</strong></div>
                <div><span>Pending After Save</span><strong data-advance-total-preview>{{ number_format((float) old('amount', 0), 2) }}</strong></div>
            </div>
        </div>
    </div>
</div>
