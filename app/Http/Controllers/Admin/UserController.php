<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetUserPasswordRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'name' => (string) $request->string('name'),
            'email' => (string) $request->string('email'),
            'role' => (string) $request->string('role'),
            'status' => (string) $request->string('status'),
            'sort' => $request->string('sort')->toString() === 'oldest' ? 'oldest' : 'latest',
        ];

        $users = User::query()
            ->filterName($filters['name'])
            ->filterEmail($filters['email'])
            ->filterRole($filters['role'])
            ->filterStatus($filters['status'])
            ->sortByCreated($filters['sort'])
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'filters' => $filters,
            'roles' => $this->normalizeOptionLabels(User::roleOptions()),
            'statuses' => $this->normalizeOptionLabels(User::statusOptions()),
        ]);
    }

    public function create(): View
    {
        return view('users.create', [
            'roles' => $this->normalizeOptionLabels(User::roleOptions()),
            'statuses' => $this->normalizeOptionLabels(User::statusOptions()),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => (string) $request->input('name'),
            'email' => (string) $request->input('email'),
            'role' => (string) $request->input('role'),
            'status' => (string) $request->input('status'),
            'password' => Hash::make((string) $request->input('password')),
        ]);

        return redirect()
            ->route('users.show', $user)
            ->with('status', 'User account created successfully.');
    }

    public function show(User $user): View
    {
        return view('users.show', ['user' => $user]);
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => $this->normalizeOptionLabels(User::roleOptions()),
            'statuses' => $this->normalizeOptionLabels(User::statusOptions()),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->guardAdminIntegrity($user, $request);

        $user->update($request->validated());

        return redirect()
            ->route('users.show', $user)
            ->with('status', 'User account updated successfully.');
    }

    public function updatePassword(ResetUserPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make((string) $request->input('password')),
        ]);

        return redirect()
            ->route('users.show', $user)
            ->with('status', 'Password reset successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors([
                'user' => 'You cannot change the status of your own account.',
            ]);
        }

        if ($user->isAdmin() && $user->isActive() && User::query()
            ->where('role', User::ROLE_ADMIN)
            ->where('status', User::STATUS_ACTIVE)
            ->count() <= 1) {
            return back()->withErrors([
                'user' => 'The last active admin account cannot be deactivated.',
            ]);
        }

        $user->update([
            'status' => $user->isActive() ? User::STATUS_INACTIVE : User::STATUS_ACTIVE,
        ]);

        return back()->with('status', 'User status updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->withErrors([
                'user' => 'Admin accounts cannot be deleted.',
            ]);
        }

        if ($user->is(auth()->user())) {
            return back()->withErrors([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        DB::table('sessions')->where('user_id', $user->id)->delete();
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('status', 'User account deleted successfully.');
    }

    protected function guardAdminIntegrity(User $user, UpdateUserRequest $request): void
    {
        $newRole = (string) $request->input('role');
        $newStatus = (string) $request->input('status');

        if ($user->is(auth()->user()) && $newRole !== User::ROLE_ADMIN) {
            throw ValidationException::withMessages([
                'role' => 'You cannot remove your own admin access.',
            ]);
        }

        if ($user->is(auth()->user()) && $newStatus !== User::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'status' => 'You cannot deactivate your own account.',
            ]);
        }

        if (! $user->isAdmin()) {
            return;
        }

        $activeAdminCount = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->where('status', User::STATUS_ACTIVE)
            ->count();

        $adminCount = User::query()->where('role', User::ROLE_ADMIN)->count();

        if ($newRole !== User::ROLE_ADMIN && $adminCount <= 1) {
            throw ValidationException::withMessages([
                'role' => 'The last remaining admin cannot be reassigned.',
            ]);
        }

        if ($newStatus !== User::STATUS_ACTIVE && $user->isActive() && $activeAdminCount <= 1) {
            throw ValidationException::withMessages([
                'status' => 'The last active admin account cannot be deactivated.',
            ]);
        }
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
                            return [$value => $label[$key]];
                        }
                    }
                }

                $label = is_string($label)
                    ? $label
                    : ucfirst(str_replace('_', ' ', $value));

                $roleKey = 'roles.'.$value;
                $translatedRole = __($roleKey);

                if (is_string($translatedRole) && $translatedRole !== $roleKey) {
                    return [$value => $translatedRole];
                }

                $translatedLabel = __($label);

                return [$value => is_string($translatedLabel) ? $translatedLabel : $label];
            })
            ->all();
    }
}
