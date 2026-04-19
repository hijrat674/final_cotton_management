@php
    $sale = $sale ?? null;
    $saleItems = old('items', $sale?->items?->map(fn ($item) => [
        'inventory_item_id' => $item->inventory_item_id,
        'quantity' => number_format((float) $item->quantity, 3, '.', ''),
        'unit_price' => number_format((float) $item->unit_price, 2, '.', ''),
    ])->toArray() ?? [[]]);
@endphp

<div class="row g-4" data-sale-form>
    <div class="col-xl-8">
        <div class="sale-form-card mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select id="customer_id" name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                        <option value="">Select customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) old('customer_id', $sale->customer_id ?? request('customer_id')) === (string) $customer->id)>
                                {{ $customer->full_name }} - {{ $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="sale_date" class="form-label">Sale Date</label>
                    <input type="date" id="sale_date" name="sale_date" value="{{ old('sale_date', isset($sale) ? $sale->sale_date->toDateString() : now()->toDateString()) }}" class="form-control @error('sale_date') is-invalid @enderror" required>
                    @error('sale_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="paid_amount" class="form-label">Initial Payment</label>
                    <input type="number" step="0.01" min="0" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', isset($sale) ? number_format((float) $sale->paid_amount, 2, '.', '') : '0.00') }}" class="form-control @error('paid_amount') is-invalid @enderror" data-sale-paid required>
                    @error('paid_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $sale->notes ?? '') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="sale-form-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="section-title mb-1">Sale Items</h3>
                    <p class="section-text mb-0">Add finished goods or saleable stock items to this invoice.</p>
                </div>
                <button type="button" class="btn btn-outline-primary" data-add-sale-item>Add Item</button>
            </div>

            @error('items')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <div class="d-grid gap-3" data-sale-item-rows>
                @foreach($saleItems as $index => $row)
                    <div class="sale-item-row" data-sale-item-row>
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-4">
                                <label class="form-label">Inventory Item</label>
                                <select name="items[{{ $index }}][inventory_item_id]" class="form-select @error("items.$index.inventory_item_id") is-invalid @enderror" data-sale-item-select required>
                                    <option value="">Select item</option>
                                    @foreach($inventoryItems as $inventoryItem)
                                        <option value="{{ $inventoryItem->id }}" data-stock="{{ number_format($inventoryItem->current_stock, 3, '.', '') }}" data-unit="{{ $inventoryItem->unit }}" @selected((string) ($row['inventory_item_id'] ?? '') === (string) $inventoryItem->id)>
                                            {{ $inventoryItem->product_name }} ({{ number_format($inventoryItem->current_stock, 3) }} {{ strtoupper($inventoryItem->unit) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error("items.$index.inventory_item_id")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label">Available Stock</label>
                                <div class="form-control bg-light" data-sale-stock-display>0.000</div>
                            </div>

                            <div class="col-lg-1">
                                <label class="form-label">Unit</label>
                                <div class="form-control bg-light" data-sale-unit-display>--</div>
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label">Quantity</label>
                                <input type="number" step="0.001" min="0.001" name="items[{{ $index }}][quantity]" value="{{ $row['quantity'] ?? '' }}" class="form-control @error("items.$index.quantity") is-invalid @enderror" data-sale-quantity required>
                                @error("items.$index.quantity")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label">Unit Price</label>
                                <input type="number" step="0.01" min="0.01" name="items[{{ $index }}][unit_price]" value="{{ $row['unit_price'] ?? '' }}" class="form-control @error("items.$index.unit_price") is-invalid @enderror" data-sale-unit-price required>
                                @error("items.$index.unit_price")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-1">
                                <button type="button" class="btn btn-outline-danger w-100" data-remove-sale-item>&times;</button>
                            </div>
                        </div>

                        <div class="sale-item-total mt-2">
                            <span>Line Total</span>
                            <strong data-sale-line-total>0.00</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="sale-form-card sale-summary-card">
            <h3 class="section-title mb-3">Invoice Summary</h3>
            <div class="summary-breakdown">
                <div>
                    <span>Grand Total</span>
                    <strong data-sale-grand-total>0.00</strong>
                </div>
                <div>
                    <span>Paid Amount</span>
                    <strong data-sale-paid-preview>0.00</strong>
                </div>
                <div>
                    <span>Remaining Balance</span>
                    <strong data-sale-remaining>0.00</strong>
                </div>
            </div>

            <p class="section-text mt-3 mb-0">
                Stock is validated against live inventory. The sale cannot be saved if any item exceeds available stock.
            </p>
        </div>
    </div>

    <template data-sale-item-template>
        <div class="sale-item-row" data-sale-item-row>
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label">Inventory Item</label>
                    <select name="items[__INDEX__][inventory_item_id]" class="form-select" data-sale-item-select required>
                        <option value="">Select item</option>
                        @foreach($inventoryItems as $inventoryItem)
                            <option value="{{ $inventoryItem->id }}" data-stock="{{ number_format($inventoryItem->current_stock, 3, '.', '') }}" data-unit="{{ $inventoryItem->unit }}">
                                {{ $inventoryItem->product_name }} ({{ number_format($inventoryItem->current_stock, 3) }} {{ strtoupper($inventoryItem->unit) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Available Stock</label>
                    <div class="form-control bg-light" data-sale-stock-display>0.000</div>
                </div>

                <div class="col-lg-1">
                    <label class="form-label">Unit</label>
                    <div class="form-control bg-light" data-sale-unit-display>--</div>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" step="0.001" min="0.001" name="items[__INDEX__][quantity]" class="form-control" data-sale-quantity required>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Unit Price</label>
                    <input type="number" step="0.01" min="0.01" name="items[__INDEX__][unit_price]" class="form-control" data-sale-unit-price required>
                </div>

                <div class="col-lg-1">
                    <button type="button" class="btn btn-outline-danger w-100" data-remove-sale-item>&times;</button>
                </div>
            </div>

            <div class="sale-item-total mt-2">
                <span>Line Total</span>
                <strong data-sale-line-total>0.00</strong>
            </div>
        </div>
    </template>
</div>
