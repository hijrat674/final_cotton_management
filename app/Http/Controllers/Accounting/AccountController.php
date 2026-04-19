<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\AccountingFilterRequest;
use App\Models\Account;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(AccountingFilterRequest $request): View
    {
        $filters = $request->validated() + [
            'account_type' => $request->input('account_type'),
            'search' => $request->input('search'),
        ];

        $accounts = Account::query()
            ->with('parent')
            ->withSum('journalEntryLines', 'debit')
            ->withSum('journalEntryLines', 'credit')
            ->filterType($filters['account_type'] ?? null)
            ->filterSearch($filters['search'] ?? null)
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return view('accounting.accounts.index', [
            'accounts' => $accounts,
            'filters' => $filters,
            'accountTypes' => Account::typeOptions(),
        ]);
    }
}
