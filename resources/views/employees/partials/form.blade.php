@php
    $employee = $employee ?? null;
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="employee-form-card">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">{{ __('employees.fields.full_name') }}</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $employee->full_name ?? '') }}" class="form-control @error('full_name') is-invalid @enderror" required>
                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">{{ __('employees.fields.phone') }}</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror" required>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="position" class="form-label">{{ __('employees.fields.position') }}</label>
                    <input type="text" id="position" name="position" value="{{ old('position', $employee->position ?? '') }}" class="form-control @error('position') is-invalid @enderror" required>
                    @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="department" class="form-label">{{ __('employees.fields.department') }}</label>
                    <input type="text" id="department" name="department" value="{{ old('department', $employee->department ?? '') }}" class="form-control @error('department') is-invalid @enderror" required>
                    @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="salary" class="form-label">{{ __('employees.fields.salary') }}</label>
                    <input type="number" step="0.01" min="0" id="salary" name="salary" value="{{ old('salary', isset($employee) ? number_format((float) $employee->salary, 2, '.', '') : '') }}" class="form-control @error('salary') is-invalid @enderror" required data-salary-input>
                    @error('salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="hire_date" class="form-label">{{ __('employees.fields.hire_date') }}</label>
                    <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date', isset($employee) ? $employee->hire_date->toDateString() : now()->toDateString()) }}" class="form-control @error('hire_date') is-invalid @enderror" required>
                    @error('hire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">{{ __('employees.fields.status') }}</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $employee->status ?? \App\Models\Employee::STATUS_ACTIVE) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="user_id" class="form-label">{{ __('employees.fields.linked_user_account') }}</label>
                    <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                        <option value="">{{ __('employees.messages.no_linked_user_account') }}</option>
                        @foreach($availableUsers as $user)
                            <option value="{{ $user->id }}" @selected((string) old('user_id', $employee->user_id ?? '') === (string) $user->id)>{{ $user->name }} - {{ $user->email }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">{{ __('employees.form.linked_user_help') }}</div>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="address" class="form-label">{{ __('employees.fields.address') }}</label>
                    <textarea id="address" name="address" rows="4" class="form-control @error('address') is-invalid @enderror">{{ old('address', $employee->address ?? '') }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="employee-form-card employee-form-help">
            <h3 class="section-title mb-3">{{ __('employees.form.guidance_title') }}</h3>
            <ul class="employee-guidance-list mb-0">
                <li>{{ __('employees.form.guidance.active_only') }}</li>
                <li>{{ __('employees.form.guidance.linked_user') }}</li>
                <li>{{ __('employees.form.guidance.salary') }}</li>
            </ul>
        </div>
    </div>
</div>
