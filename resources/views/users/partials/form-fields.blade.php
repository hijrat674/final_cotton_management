<div class="row g-4">
    <div class="col-md-6">
        <label for="name" class="form-label">{{ __('users.form.name') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">{{ __('users.form.email') }}</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="role" class="form-label">{{ __('users.form.role') }}</label>
        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
            @foreach($roles as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">{{ __('users.form.status') }}</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $user->status ?? \App\Models\User::STATUS_ACTIVE) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @isset($includePasswordFields)
        <div class="col-md-6">
            <label for="password" class="form-label">{{ __('users.password.new_password') }}</label>
            <div class="password-field">
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                <button type="button" class="password-toggle" data-password-toggle data-target="password" data-label-show="{{ __('users.password.show') }}" data-label-hide="{{ __('users.password.hide') }}">{{ __('users.password.show') }}</button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="password_confirmation" class="form-label">{{ __('users.password.confirm_password') }}</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>
    @endisset
</div>
