<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounting\AccountingFilterRequest;
use App\Models\JournalEntry;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    public function index(AccountingFilterRequest $request): View
    {
        $filters = $request->validated() + [
            'reference_type' => $request->input('reference_type'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $entries = JournalEntry::query()
            ->with(['creator', 'lines.account'])
            ->filterReferenceType($filters['reference_type'] ?? null)
            ->filterDateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null)
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('accounting.journal_entries.index', [
            'entries' => $entries,
            'filters' => $filters,
            'referenceTypes' => JournalEntry::referenceTypeOptions(),
        ]);
    }

    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load(['creator', 'lines.account']);

        return view('accounting.journal_entries.show', [
            'entry' => $journalEntry,
        ]);
    }
}
