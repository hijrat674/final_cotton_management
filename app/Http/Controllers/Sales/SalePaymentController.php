<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreSalePaymentRequest;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\Sales\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalePaymentController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {
    }

    public function create(Sale $sale): View
    {
        $sale->load('customer');

        return view('sale_payments.create', [
            'sale' => $sale,
            'paymentMethods' => SalePayment::paymentMethodOptions(),
        ]);
    }

    public function store(StoreSalePaymentRequest $request): RedirectResponse
    {
        $sale = Sale::query()->findOrFail($request->integer('sale_id'));
        $this->saleService->recordPayment($sale, $request->validated(), $request->user()->id);

        return redirect()
            ->route('sales.show', $sale)
            ->with('status', 'Payment collected successfully and the invoice balance was updated.');
    }
}
