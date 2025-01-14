<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleManagementController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('pages.role-management.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('pages.role-management.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('role-management.index')->with('success', 'Role berhasil dibuat.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('pages.role-management.form', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('role-management.index')->with('success', 'Role berhasil diupdate.');
    }

    public function destroy(Role $role)
    {
        // Cek apakah ada user aktif dengan role ini
        if ($role->users()->where('is_active', true)->exists()) {
            return redirect()->route('role-management.index')->withErrors([
                'error' => 'Role tidak dapat dihapus karena masih ada akun aktif dengan role ini.',
            ]);
        }

        // Hapus role jika tidak ada user aktif
        $role->delete();
        return redirect()->route('role-management.index')->with('success', 'Role berhasil dihapus.');
    }
}
