<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereNull('deleted_at');

        // Filter by role
        $role = $request->input('role');
        if ($role) {
            $query->where('roles.id', $role);
        }

        // Filter by status
        $status = $request->input('is_active');
        if ($status !== null) {
            $query->where('is_active', $status);
        }

        $userList = $query->orderBy('name', 'asc')
            ->paginate(10);

        return view('user.index', compact('userList'));
    }

    public function create(Request $request)
    {
        return view('user.form');
    }

    public function store(StoreUserRequest $request)
    {
        User::create($request->validated());

        AuditLogService::log('create', $request->user(), null, null, null, 'User dibuat');

        return redirect()->route('user.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user)
    {
        return view('user.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('user.form', compact('user'));
    }

    public function update(StoreUserRequest $request, User $user)
    {
        $user->update($request->validated());

        AuditLogService::log('update', $user, null, null, $request->validated(), 'User diperbarui');

        return redirect()->route('user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        // Tidak bisa hapus akun sendiri
        if ($user->id === auth()->id()) {
            abort(403, 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        AuditLogService::log('delete', $user, null, null, null, 'User dihapus');

        return redirect()->route('user.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'Tidak bisa mengubah status akun sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        AuditLogService::log('update', $user, null, ['is_active' => $user->is_active], $request->validated(), 'Status user diubah');

        return redirect()->route('user.index')
            ->with('success', 'Status user berhasil diubah.');
    }

    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate($request->route()->getValidatorInstance());

        $roles = $validated['roles'];

        // Sync roles via Spatie
        $user->syncRoles($roles);

        AuditLogService::log('update', $user, null, ['roles' => $user->roles->pluck('id')->toArray()], $roles, 'Role user diubah');

        return redirect()->route('user.index')
            ->with('success', 'Role user berhasil diubah.');
    }
}
