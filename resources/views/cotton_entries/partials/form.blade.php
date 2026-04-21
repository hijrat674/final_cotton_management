<div class="row g-3 cotton-entry-form-grid">
    <div class="col-md-6">
        <label for="truck_number" class="form-label">{{ __('cotton.fields.truck_number') }}</label>
        <input type="text" id="truck_number" name="truck_number" value="{{ old('truck_number', $entry->truck_number ?? '') }}" class="form-control @error('truck_number') is-invalid @enderror" required>
        @error('truck_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="inventory_item_id" class="form-label">{{ __('cotton.fields.material_category') }}</label>
        <select id="inventory_item_id" name="inventory_item_id" class="form-select @error('inventory_item_id') is-invalid @enderror" required @disabled($lockInventoryItem ?? false)>
            <option value="">{{ __('cotton.form.select_material_category') }}</option>
            @foreach($inventoryItems as $item)
                <option value="{{ $item->id }}" @selected((string) old('inventory_item_id', $entry->inventory_item_id ?? '') === (string) $item->id)>
                    {{ $item->product_name }}
                </option>
            @endforeach
        </select>
        @if($lockInventoryItem ?? false)
            <input type="hidden" name="inventory_item_id" value="{{ old('inventory_item_id', $entry->inventory_item_id ?? '') }}">
            <div class="form-text">{{ __('cotton.form.material_locked') }}</div>
        @endif
        @error('inventory_item_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="driver_name" class="form-label">{{ __('cotton.fields.driver_name') }}</label>
        <input type="text" id="driver_name" name="driver_name" value="{{ old('driver_name', $entry->driver_name ?? '') }}" class="form-control @error('driver_name') is-invalid @enderror" required>
        @error('driver_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="driver_phone" class="form-label">{{ __('cotton.fields.driver_phone') }}</label>
        <input type="text" id="driver_phone" name="driver_phone" value="{{ old('driver_phone', $entry->driver_phone ?? '') }}" class="form-control @error('driver_phone') is-invalid @enderror">
        @error('driver_phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="gross_weight" class="form-label">{{ __('cotton.fields.gross_weight') }}</label>
        <input type="number" step="0.001" min="0" id="gross_weight" name="gross_weight" value="{{ old('gross_weight', isset($entry) ? number_format((float) $entry->gross_weight, 3, '.', '') : '') }}" class="form-control @error('gross_weight') is-invalid @enderror" required data-gross-weight>
        @error('gross_weight')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="tare_weight" class="form-label">{{ __('cotton.fields.tare_weight') }}</label>
        <input type="number" step="0.001" min="0" id="tare_weight" name="tare_weight" value="{{ old('tare_weight', isset($entry) ? number_format((float) $entry->tare_weight, 3, '.', '') : '') }}" class="form-control @error('tare_weight') is-invalid @enderror" required data-tare-weight>
        @error('tare_weight')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="net_weight" class="form-label">{{ __('cotton.fields.net_weight') }}</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-calculator"></i></span>
            <input type="number" step="0.001" id="net_weight" value="{{ old('net_weight', isset($entry) ? number_format((float) $entry->net_weight, 3, '.', '') : '0.000') }}" class="form-control net-weight-field" readonly data-net-weight>
        </div>
        <div class="form-text">{{ __('cotton.form.net_weight_help') }}</div>
    </div>

    <div class="col-md-6">
        <label for="entry_date" class="form-label">{{ __('cotton.fields.entry_date') }}</label>
        <input type="date" id="entry_date" name="entry_date" value="{{ old('entry_date', isset($entry) ? $entry->entry_date->toDateString() : now()->toDateString()) }}" class="form-control @error('entry_date') is-invalid @enderror" required>
        @error('entry_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">{{ __('cotton.fields.notes') }}</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $entry->notes ?? '') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
