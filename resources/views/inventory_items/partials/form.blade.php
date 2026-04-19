<div class="row g-4">
    <div class="col-md-6">
        <label for="product_name" class="form-label">Product Name</label>
        <input type="text" id="product_name" name="product_name" value="{{ old('product_name', $item->product_name ?? '') }}" class="form-control @error('product_name') is-invalid @enderror" required>
        @error('product_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="product_code" class="form-label">Product Code</label>
        <input type="text" id="product_code" name="product_code" value="{{ old('product_code', $item->product_code ?? '') }}" class="form-control @error('product_code') is-invalid @enderror">
        @error('product_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="product_type" class="form-label">Product Type</label>
        <select id="product_type" name="product_type" class="form-select @error('product_type') is-invalid @enderror" required>
            @foreach($productTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('product_type', $item->product_type ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('product_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="unit" class="form-label">Unit</label>
        <select id="unit" name="unit" class="form-select @error('unit') is-invalid @enderror" required>
            @foreach($units as $value => $label)
                <option value="{{ $value }}" @selected(old('unit', $item->unit ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('unit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="minimum_stock" class="form-label">Minimum Stock</label>
        <input type="number" step="0.001" min="0" id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock ?? '0.000') }}" class="form-control @error('minimum_stock') is-invalid @enderror" required>
        @error('minimum_stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $item->notes ?? '') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
