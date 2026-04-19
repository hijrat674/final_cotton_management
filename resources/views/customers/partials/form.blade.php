@php
    $customer = $customer ?? null;
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="customer-form-card">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Customer Name</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $customer->full_name ?? '') }}" class="form-control @error('full_name') is-invalid @enderror" required>
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $customer->address ?? '') }}" class="form-control @error('address') is-invalid @enderror">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" rows="5" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $customer->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="customer-form-card customer-form-help">
            <h3 class="section-title mb-3">Customer Master Tips</h3>
            <ul class="customer-guidance-list mb-0">
                <li>Use the customer’s full legal or trade name for invoice consistency.</li>
                <li>Keep phone numbers current so collections and follow-up are easier.</li>
                <li>Use notes for delivery preferences, account remarks, or credit reminders.</li>
            </ul>
        </div>
    </div>
</div>
