<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'name' => (string) $request->string('name'),
            'phone' => (string) $request->string('phone'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $query = Customer::query()
            ->withSum('sales', 'remaining_amount')
            ->withCount('sales')
            ->filterName($filters['name'])
            ->filterPhone($filters['phone']);

        $summaryCustomers = (clone $query)->get();

        $customers = $query
            ->sortByCreated($filters['sort'])
            ->paginate(12)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'filters' => $filters,
            'summary' => [
                'total_customers' => $summaryCustomers->count(),
                'customers_with_balance' => $summaryCustomers->filter(fn (Customer $customer) => $customer->outstanding_balance > 0)->count(),
                'total_outstanding' => round((float) $summaryCustomers->sum('outstanding_balance'), 2),
            ],
            'canManageCustomers' => $request->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_SALES) ?? false,
            'canDeleteCustomers' => $request->user()?->hasRole(User::ROLE_ADMIN) ?? false,
        ]);
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'sales' => fn ($query) => $query
                ->with(['seller'])
                ->latest('sale_date')
                ->latest('id'),
            'salePayments' => fn ($query) => $query
                ->with(['sale', 'receiver'])
                ->latest('payment_date')
                ->latest('id'),
        ]);

        return view('customers.show', [
            'customer' => $customer,
            'canManageCustomers' => auth()->user()?->hasRole(User::ROLE_ADMIN, User::ROLE_SALES) ?? false,
            'canDeleteCustomer' => auth()->user()?->hasRole(User::ROLE_ADMIN) && ! $customer->hasSalesHistory(),
            'summary' => [
                'total_sales' => $customer->sales->count(),
                'total_revenue' => round((float) $customer->sales->sum('total_amount'), 2),
                'paid_amount' => round((float) $customer->sales->sum('paid_amount'), 2),
                'outstanding' => round((float) $customer->sales->sum('remaining_amount'), 2),
            ],
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', ['customer' => $customer]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()
            ->route('customers.show', $customer)
            ->with('status', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->hasSalesHistory()) {
            return redirect()
                ->route('customers.show', $customer)
                ->withErrors([
                    'customer' => 'This customer cannot be deleted because sales history already exists.',
                ]);
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('status', 'Customer deleted successfully.');
    }
}
