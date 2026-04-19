<?php

namespace App\Http\Controllers\Cotton;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cotton\StoreCottonEntryRequest;
use App\Http\Requests\Cotton\UpdateCottonEntryRequest;
use App\Models\CottonEntry;
use App\Models\InventoryItem;
use App\Models\User;
use App\Services\Cotton\CottonEntryInventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CottonEntryController extends Controller
{
    public function __construct(
        protected CottonEntryInventoryService $inventoryService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'truck_number' => (string) $request->string('truck_number'),
            'driver_name' => (string) $request->string('driver_name'),
            'date_from' => (string) $request->string('date_from'),
            'date_to' => (string) $request->string('date_to'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $entries = CottonEntry::query()
            ->with(['inventoryItem', 'creator', 'inventoryTransaction'])
            ->filterTruckNumber($filters['truck_number'])
            ->filterDriverName($filters['driver_name'])
            ->filterEntryDateRange($filters['date_from'], $filters['date_to'])
            ->sortByEntryDate($filters['sort'])
            ->paginate(10)
            ->withQueryString();

        return view('cotton_entries.index', [
            'entries' => $entries,
            'filters' => $filters,
            'canManageEntries' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
            'canCreateEntries' => $request->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_PRODUCTION) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('cotton_entries.create', [
            'inventoryItems' => InventoryItem::query()->orderBy('product_name')->get(),
        ]);
    }

    public function store(StoreCottonEntryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['net_weight'] = $this->calculateNetWeight(
            (float) $validated['gross_weight'],
            (float) $validated['tare_weight'],
        );
        $validated['created_by'] = $request->user()->id;

        $entry = $this->inventoryService->createEntryWithTransaction($validated);

        return redirect()
            ->route('cotton-entries.show', $entry)
            ->with('status', 'Cotton entry recorded successfully and stock intake was posted to inventory.');
    }

    public function show(CottonEntry $cottonEntry): View
    {
        $cottonEntry->load(['inventoryItem', 'creator', 'inventoryTransaction']);

        return view('cotton_entries.show', [
            'entry' => $cottonEntry,
            'canManageEntries' => auth()->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function edit(CottonEntry $cottonEntry): View
    {
        $cottonEntry->load(['inventoryItem', 'inventoryTransaction']);

        return view('cotton_entries.edit', [
            'entry' => $cottonEntry,
            'inventoryItems' => InventoryItem::query()->orderBy('product_name')->get(),
            'lockInventoryItem' => true,
        ]);
    }

    public function update(UpdateCottonEntryRequest $request, CottonEntry $cottonEntry): RedirectResponse
    {
        $cottonEntry->load(['inventoryItem', 'inventoryTransaction']);

        $validated = $request->validated();
        $validated['net_weight'] = $this->calculateNetWeight(
            (float) $validated['gross_weight'],
            (float) $validated['tare_weight'],
        );
        $cottonEntry = $this->inventoryService->updateEntryWithTransaction($cottonEntry, $validated);

        return redirect()
            ->route('cotton-entries.show', $cottonEntry)
            ->with('status', 'Cotton entry updated successfully and the linked inventory intake was synchronized.');
    }

    public function destroy(CottonEntry $cottonEntry): RedirectResponse
    {
        $cottonEntry->load(['inventoryItem', 'inventoryTransaction']);
        $this->inventoryService->deleteEntryWithTransaction($cottonEntry);

        return redirect()
            ->route('cotton-entries.index')
            ->with('status', 'Cotton entry deleted successfully and the linked inventory intake was removed safely.');
    }

    protected function calculateNetWeight(float $grossWeight, float $tareWeight): float
    {
        return round($grossWeight - $tareWeight, 3);
    }
}
