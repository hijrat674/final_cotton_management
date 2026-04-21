<section class="summary-showcase inventory-summary-section content-card mt-4">
    <div class="users-summary-header">
        <div>
            <h3 class="users-summary-title">{{ __('dashboard.inventory.title') }}</h3>
            <p class="users-summary-text mb-0">{{ __('dashboard.inventory.text') }}</p>
        </div>
        <a href="{{ route('inventory-items.index') }}" class="btn btn-outline-primary users-summary-action">{{ __('dashboard.inventory.open') }}</a>
    </div>

    <div class="row g-4 users-summary-grid">
        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card inventory-summary-card-total">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-box-seam-fill"></i>
                </span>
                <div class="users-summary-copy inventory-summary-copy">
                    <div class="inventory-summary-heading">
                        <span class="users-summary-label">{{ __('dashboard.inventory.total_items') }}</span>
                    </div>
                    <div class="users-summary-value-wrap inventory-summary-value-wrap">
                        <strong class="users-summary-value inventory-summary-value">{{ $inventorySummary['total_items'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card inventory-summary-card-low">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </span>
                <div class="users-summary-copy inventory-summary-copy">
                    <div class="inventory-summary-heading">
                        <span class="users-summary-label">{{ __('dashboard.inventory.low_stock') }}</span>
                    </div>
                    <div class="users-summary-value-wrap inventory-summary-value-wrap">
                        <strong class="users-summary-value inventory-summary-value">{{ $inventorySummary['low_stock_count'] }}</strong>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-sm-6 col-xl-3">
            <article class="users-summary-card inventory-summary-card-empty">
                <span class="users-summary-icon" aria-hidden="true">
                    <i class="bi bi-x-octagon-fill"></i>
                </span>
                <div class="users-summary-copy inventory-summary-copy">
                    <div class="inventory-summary-heading">
                        <span class="users-summary-label">{{ __('dashboard.inventory.out_of_stock') }}</span>
                    </div>
                    <div class="users-summary-value-wrap inventory-summary-value-wrap">
                        <strong class="users-summary-value inventory-summary-value">{{ $inventorySummary['out_of_stock_count'] }}</strong>
                    </div>
                </div>
            </article>
        </div>
    </div>

    @if(!empty($inventorySummary['items']))
        <div class="inventory-item-summary-head">
            <div>
                <h4 class="users-summary-title inventory-item-summary-title">{{ __('dashboard.inventory.by_item_title') }}</h4>
                <p class="users-summary-text mb-0">{{ __('dashboard.inventory.by_item_text') }}</p>
            </div>
        </div>

        @php
            $inventoryItemIcons = [
                'Raw Cotton' => ['icon' => 'bi-flower1', 'class' => 'inventory-item-card-raw-cotton'],
                'Processed Cotton' => ['icon' => 'bi-box-seam-fill', 'class' => 'inventory-item-card-processed-cotton'],
                'Kernel' => ['icon' => 'bi-circle-fill', 'class' => 'inventory-item-card-kernel'],
                'Cotton Oil' => ['icon' => 'bi-droplet-fill', 'class' => 'inventory-item-card-cotton-oil'],
                'Cotton Meal' => ['icon' => 'bi-archive-fill', 'class' => 'inventory-item-card-cotton-meal'],
                'Waste' => ['icon' => 'bi-trash3-fill', 'class' => 'inventory-item-card-waste'],
                'Shell' => ['icon' => 'bi-layers-fill', 'class' => 'inventory-item-card-shell'],
            ];
            $inventoryTypeIcons = [
                'raw_material' => ['icon' => 'bi-box-fill', 'class' => 'inventory-item-card-raw-material'],
                'semi_finished' => ['icon' => 'bi-layers-fill', 'class' => 'inventory-item-card-semi-finished'],
                'finished_product' => ['icon' => 'bi-box-seam-fill', 'class' => 'inventory-item-card-finished-product'],
                'byproduct' => ['icon' => 'bi-droplet-half', 'class' => 'inventory-item-card-byproduct'],
                'waste' => ['icon' => 'bi-exclamation-octagon-fill', 'class' => 'inventory-item-card-waste'],
            ];
        @endphp

        <div class="row g-4 users-summary-grid mt-1">
            @foreach($inventorySummary['items'] as $item)
                @php
                    $iconConfig = $inventoryItemIcons[$item['name']]
                        ?? $inventoryTypeIcons[$item['type']]
                        ?? ['icon' => 'bi-box-fill', 'class' => 'inventory-item-card-default'];
                @endphp
                <div class="col-sm-6 col-xl-3">
                    <article class="users-summary-card inventory-item-card {{ $iconConfig['class'] }}">
                        <span class="users-summary-icon" aria-hidden="true">
                            <i class="bi {{ $iconConfig['icon'] }}"></i>
                        </span>
                        <div class="users-summary-copy inventory-summary-copy">
                            <div class="inventory-summary-heading">
                                <span class="users-summary-label inventory-item-label">{{ $item['name'] }}</span>
                            </div>
                            <div class="users-summary-value-wrap inventory-summary-value-wrap">
                                <strong class="users-summary-value inventory-stock-line">
                                    <span class="inventory-stock-value">{{ $item['formatted_quantity'] }}</span>
                                    <span class="inventory-stock-unit">{{ $item['unit'] }}</span>
                                </strong>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @endif
</section>
