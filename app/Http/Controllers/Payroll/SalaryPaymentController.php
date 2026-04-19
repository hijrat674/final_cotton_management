<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\StoreSalaryPaymentRequest;
use App\Models\SalaryRecord;
use App\Services\Payroll\PayrollService;
use Illuminate\Http\RedirectResponse;

class SalaryPaymentController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {
    }

    public function store(StoreSalaryPaymentRequest $request): RedirectResponse
    {
        $salaryRecord = SalaryRecord::query()->findOrFail($request->integer('salary_record_id'));
        $this->payrollService->recordPayment($salaryRecord, $request->validated(), $request->user()->id);

        return redirect()
            ->route('salary-records.show', $salaryRecord)
            ->with('status', 'Salary payment recorded successfully. Expense and accounting entries were generated automatically.');
    }
}
