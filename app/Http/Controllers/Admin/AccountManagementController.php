<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountManagementController extends Controller
{
    // Tampilkan daftar akun
    public function index()
    {
        $users = User::all();
        return view('pages.account-management.index', compact('users'));
    }

    // Tampilkan form tambah akun
    public function create()
    {
        $roles = Role::all();
        return view('pages.account-management.form', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:255',
            'role' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $joinDate = Carbon::today();

        $userId = User::generateUserId($joinDate);

        $user = User::create([
            'user_id' => $userId,
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->attach($validated['role']); // Assign role
        return redirect()->route('account-management.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all(); // Ambil semua role
        return view('pages.account-management.form', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'role' => 'required|exists:roles,id', // Validasi role
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
        ]);

        $user->roles()->sync([$validated['role']]); // Update role
        return redirect()->route('account-management.index')->with('success', 'Akun berhasil diupdate.');
    }

    public function toggleStatus(User $user)
    {
        // Periksa permission
        if (!auth()->user()->hasPermission('manage_account_status')) {
            return redirect()->route('account-management.index')
                ->withErrors(['error' => 'Anda tidak memiliki izin untuk mengubah status akun.']);
        }

        // Cek apakah role user masih valid
        if (!$user->roles()->exists()) {
            return redirect()->route('account-management.index')
                ->withErrors(['error' => 'Akun ini memiliki role yang tidak valid. Harap perbaiki sebelum mengubah status.']);
        }

        // Toggle status
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('account-management.index')
            ->with('success', "Akun berhasil $status.");
    }
}
