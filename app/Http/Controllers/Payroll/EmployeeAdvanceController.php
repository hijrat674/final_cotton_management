<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\StoreEmployeeAdvanceRequest;
use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Models\User;
use App\Services\Payroll\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeAdvanceController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'employee_id' => (string) $request->string('employee_id'),
            'status' => (string) $request->string('status'),
        ];

        $query = EmployeeAdvance::query()
            ->with(['employee', 'creator'])
            ->filterEmployee($filters['employee_id'])
            ->filterStatus($filters['status'])
            ->latest('advance_date')
            ->latest('id');

        $summaryRecords = (clone $query)->get();

        return view('employee_advances.index', [
            'advances' => $query->paginate(12)->withQueryString(),
            'filters' => $filters,
            'employees' => Employee::query()->orderBy('full_name')->get(),
            'statuses' => EmployeeAdvance::statusOptions(),
            'summary' => [
                'total_advances' => round((float) $summaryRecords->sum('amount'), 2),
                'pending_advances' => round((float) $summaryRecords->where('status', EmployeeAdvance::STATUS_PENDING)->sum('amount'), 2),
                'deducted_advances' => round((float) $summaryRecords->where('status', EmployeeAdvance::STATUS_DEDUCTED)->sum('amount'), 2),
            ],
            'canManageAdvances' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('employee_advances.create', [
            'employees' => Employee::query()
                ->active()
                ->withSum(['advances as pending_advances_total' => fn ($query) => $query->pending()], 'amount')
                ->orderBy('full_name')
                ->get(),
        ]);
    }

    public function store(StoreEmployeeAdvanceRequest $request): RedirectResponse
    {
        $advance = $this->payrollService->createEmployeeAdvance($request->validated(), $request->user()->id);

        return redirect()
            ->route('employee-advances.show', $advance)
            ->with('status', 'Employee advance recorded successfully. Accounting was posted automatically.');
    }

    public function show(EmployeeAdvance $employeeAdvance): View
    {
        $employeeAdvance->load(['employee.user', 'creator']);

        return view('employee_advances.show', [
            'employeeAdvance' => $employeeAdvance,
            'canManageAdvances' => auth()->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }
}
