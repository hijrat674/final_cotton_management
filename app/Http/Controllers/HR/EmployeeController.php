<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'full_name' => (string) $request->string('full_name'),
            'phone' => (string) $request->string('phone'),
            'department' => (string) $request->string('department'),
            'position' => (string) $request->string('position'),
            'status' => (string) $request->string('status'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $query = Employee::query()
            ->with(['user'])
            ->withCount('productionStages')
            ->filterName($filters['full_name'])
            ->filterPhone($filters['phone'])
            ->filterDepartment($filters['department'])
            ->filterPosition($filters['position'])
            ->filterStatus($filters['status']);

        $summaryEmployees = (clone $query)->get();

        return view('employees.index', [
            'employees' => $query->sortByCreated($filters['sort'])->paginate(12)->withQueryString(),
            'filters' => $filters,
            'departments' => Employee::query()->select('department')->distinct()->orderBy('department')->pluck('department'),
            'positions' => Employee::query()->select('position')->distinct()->orderBy('position')->pluck('position'),
            'statuses' => $this->normalizeOptionLabels(Employee::statusOptions()),
            'summary' => [
                'total_employees' => $summaryEmployees->count(),
                'active_employees' => $summaryEmployees->where('status', Employee::STATUS_ACTIVE)->count(),
                'inactive_employees' => $summaryEmployees->where('status', Employee::STATUS_INACTIVE)->count(),
                'departments_count' => $summaryEmployees->pluck('department')->filter()->unique()->count(),
            ],
            'canManageEmployees' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('employees.create', $this->formData());
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = Employee::create($request->validated());

        return redirect()
            ->route('employees.show', $employee)
            ->with('status', 'Employee created successfully.');
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'user',
            'salaryRecords' => fn ($query) => $query
                ->latest('salary_year')
                ->latest('salary_month')
                ->latest('id'),
            'advances' => fn ($query) => $query
                ->latest('advance_date')
                ->latest('id'),
            'productionStages' => fn ($query) => $query
                ->with('sourceInventoryItem')
                ->latest('stage_date')
                ->latest('id'),
        ]);

        return view('employees.show', [
            'employee' => $employee,
            'canManageEmployees' => auth()->user()?->hasRole(User::ROLE_ADMIN) ?? false,
            'canDeleteEmployee' => auth()->user()?->hasRole(User::ROLE_ADMIN) && $employee->canBeDeletedSafely(),
        ]);
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', $this->formData([
            'employee' => $employee,
        ]));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()
            ->route('employees.show', $employee)
            ->with('status', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if (! $employee->canBeDeletedSafely()) {
            return redirect()
                ->route('employees.show', $employee)
                ->withErrors([
                    'employee' => 'This employee cannot be deleted because production records are already linked to it.',
                ]);
        }

        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('status', 'Employee deleted successfully.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function formData(array $data = []): array
    {
        return [
            ...$data,
            'statuses' => $this->normalizeOptionLabels(Employee::statusOptions()),
            'availableUsers' => User::query()
                ->where(function ($query) use ($data): void {
                    $query
                        ->whereDoesntHave('employee')
                        ->when($data['employee']->user_id ?? null, fn ($builder, $userId) => $builder->orWhere('id', $userId));
                })
                ->orderBy('name')
                ->get(),
        ];
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, string>
     */
    protected function normalizeOptionLabels(array $options): array
    {
        return collect($options)
            ->mapWithKeys(function (mixed $label, string $value): array {
                if ($label instanceof Collection) {
                    $label = $label->all();
                }

                if (is_array($label)) {
                    foreach (['label', 'name', 'title', 'text', 'value'] as $key) {
                        if (isset($label[$key]) && is_string($label[$key])) {
                            $label = $label[$key];
                            break;
                        }
                    }
                }

                $label = is_string($label)
                    ? $label
                    : ucfirst(str_replace('_', ' ', $value));

                $translated = __($label);

                return [$value => is_string($translated) ? $translated : $label];
            })
            ->all();
    }
}
