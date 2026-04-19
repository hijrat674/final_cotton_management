<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\AccountingFilterRequest;
use App\Models\Account;
use App\Services\Accounting\AccountingReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected AccountingReportService $accountingReportService
    ) {
    }

    public function dashboard(AccountingFilterRequest $request): View
    {
        $filters = $request->validated();

        return view('accounting.reports.dashboard', [
            'filters' => $filters,
            'summary' => $this->accountingReportService->dashboardSummary($filters),
        ]);
    }

    public function profitAndLoss(AccountingFilterRequest $request): View
    {
        $filters = $request->validated();

        return view('accounting.reports.profit-loss', [
            'filters' => $filters,
            'report' => $this->accountingReportService->profitAndLoss($filters),
        ]);
    }

    public function cashSummary(AccountingFilterRequest $request): View
    {
        $filters = $request->validated();

        return view('accounting.reports.cash-summary', [
            'filters' => $filters,
            'report' => $this->accountingReportService->cashSummary($filters),
        ]);
    }

    public function receivables(): View
    {
        return view('accounting.reports.receivables', [
            'report' => $this->accountingReportService->customerReceivables(),
        ]);
    }

    public function generalLedger(AccountingFilterRequest $request): View
    {
        $filters = $request->validated() + [
            'account_id' => $request->input('account_id'),
        ];

        return view('accounting.reports.general-ledger', [
            'filters' => $filters,
            'accounts' => Account::query()->orderBy('code')->get(),
            'report' => $this->accountingReportService->generalLedger($filters),
        ]);
    }

    public function trialBalance(AccountingFilterRequest $request): View
    {
        $filters = $request->validated();

        return view('accounting.reports.trial-balance', [
            'filters' => $filters,
            'report' => $this->accountingReportService->trialBalance($filters),
        ]);
    }
}
