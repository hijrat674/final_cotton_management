<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\StoreSalaryRecordRequest;
use App\Models\Employee;
use App\Models\SalaryRecord;
use App\Models\EmployeeAdvance;
use App\Models\User;
use App\Services\Payroll\PayrollService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryRecordController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'employee_id' => (string) $request->string('employee_id'),
            'salary_month' => (string) $request->string('salary_month'),
            'salary_year' => (string) $request->string('salary_year'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $query = SalaryRecord::query()
            ->with(['employee'])
            ->filterEmployee($filters['employee_id'])
            ->filterMonth($filters['salary_month'])
            ->filterYear($filters['salary_year']);

        $summaryRecords = (clone $query)->get();

        return view('salary_records.index', [
            'salaryRecords' => $query->sortByPeriod($filters['sort'])->paginate(12)->withQueryString(),
            'filters' => $filters,
            'employees' => Employee::query()->orderBy('full_name')->get(),
            'months' => SalaryRecord::monthOptions(),
            'years' => SalaryRecord::query()->select('salary_year')->distinct()->orderByDesc('salary_year')->pluck('salary_year'),
            'summary' => [
                'total_salary' => round((float) $summaryRecords->sum('total_salary'), 2),
                'advance_deduction' => round((float) $summaryRecords->sum('advance_deduction'), 2),
                'paid_amount' => round((float) $summaryRecords->sum('paid_amount'), 2),
                'remaining_amount' => round((float) $summaryRecords->sum('remaining_amount'), 2),
                'employees_paid' => $summaryRecords->where('payment_status', SalaryRecord::STATUS_PAID)->pluck('employee_id')->unique()->count(),
            ],
            'canManagePayroll' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('salary_records.create', [
            'employees' => Employee::query()
                ->active()
                ->withSum(['advances as pending_advances_total' => fn ($query) => $query->pending()], 'amount')
                ->orderBy('full_name')
                ->get(),
            'months' => SalaryRecord::monthOptions(),
            'years' => range((int) now()->year - 1, (int) now()->year + 1),
        ]);
    }

    public function store(StoreSalaryRecordRequest $request): RedirectResponse
    {
        $salaryRecord = $this->payrollService->createSalaryRecord($request->validated(), $request->user()->id);

        return redirect()
            ->route('salary-records.show', $salaryRecord)
            ->with('status', 'Salary record created successfully.');
    }

    public function show(SalaryRecord $salaryRecord): View
    {
        $salaryRecord->load([
            'employee.user',
            'creator',
            'payments.receiver',
            'employee.advances' => fn ($query) => $query->latest('advance_date')->latest('id'),
        ]);

        return view('salary_records.show', [
            'salaryRecord' => $salaryRecord,
            'paymentMethods' => \App\Models\SalaryPayment::paymentMethodOptions(),
            'canManagePayroll' => auth()->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }
}
