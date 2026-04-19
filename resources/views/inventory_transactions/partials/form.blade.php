<div class="row g-4">
    <div class="col-md-6">
        <label for="inventory_item_id" class="form-label">Inventory Item</label>
        <select id="inventory_item_id" name="inventory_item_id" class="form-select @error('inventory_item_id') is-invalid @enderror" required data-transaction-item>
            <option value="">Select item</option>
            @foreach($items as $item)
                <option
                    value="{{ $item->id }}"
                    data-stock="{{ number_format($item->current_stock, 3, '.', '') }}"
                    data-unit="{{ $item->unit }}"
                    @selected((string) old('inventory_item_id', $selectedItemId ?? '') === (string) $item->id)
                >
                    {{ $item->product_name }} ({{ number_format($item->current_stock, 3) }} {{ $item->unit }})
                </option>
            @endforeach
        </select>
        @error('inventory_item_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="transaction_type" class="form-label">Transaction Type</label>
        <select id="transaction_type" name="transaction_type" class="form-select @error('transaction_type') is-invalid @enderror" required>
            @foreach($transactionTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('transaction_type') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('transaction_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="reference_type" class="form-label">Reference Type</label>
        <select id="reference_type" name="reference_type" class="form-select @error('reference_type') is-invalid @enderror">
            <option value="">No reference</option>
            @foreach($referenceTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('reference_type') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('reference_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="reference_id" class="form-label">Reference ID</label>
        <input type="number" min="1" id="reference_id" name="reference_id" value="{{ old('reference_id') }}" class="form-control @error('reference_id') is-invalid @enderror">
        @error('reference_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="quantity_in" class="form-label">Quantity In</label>
        <input type="number" step="0.001" min="0" id="quantity_in" name="quantity_in" value="{{ old('quantity_in') }}" class="form-control @error('quantity_in') is-invalid @enderror" data-quantity-in>
        @error('quantity_in')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="quantity_out" class="form-label">Quantity Out</label>
        <input type="number" step="0.001" min="0" id="quantity_out" name="quantity_out" value="{{ old('quantity_out') }}" class="form-control @error('quantity_out') is-invalid @enderror" data-quantity-out>
        @error('quantity_out')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text" id="availableStockHint">Available stock will appear after you select an item.</div>
    </div>

    <div class="col-md-6">
        <label for="transaction_date" class="form-label">Transaction Date</label>
        <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}" class="form-control @error('transaction_date') is-invalid @enderror" required>
        @error('transaction_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
