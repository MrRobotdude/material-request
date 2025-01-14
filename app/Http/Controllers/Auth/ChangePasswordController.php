<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePasswordController extends Controller
{
    /**
     * Tampilkan form untuk mengganti password.
     */
    public function showChangePasswordForm($user = null)
    {
        $currentUser = Auth::user();

        // Jika admin, izinkan melihat form untuk pengguna lain
        if ($currentUser->hasRole('admin') && $user) {
            $targetUser = User::findOrFail($user);
        } else {
            // Non-admin hanya dapat mengganti password sendiri
            $targetUser = $currentUser;
        }

        return view('pages.account-management.change-password', compact('targetUser'));
    }

    /**
     * Proses perubahan password.
     */
    public function changePassword(Request $request, $user = null)
    {
        $currentUser = Auth::user();

        // Jika admin, izinkan mengganti password pengguna lain
        if ($currentUser->hasRole('admin') && $user) {
            $targetUser = User::findOrFail($user);
        } else {
            // Non-admin hanya dapat mengganti password sendiri
            $targetUser = $currentUser;
        }

        // Validasi input
        $rules = [
            'new_password' => 'required|string|min:8|confirmed',
        ];

        // Validasi password saat ini hanya untuk pengguna biasa
        if ($targetUser->user_id === $currentUser->user_id) {
            $rules['current_password'] = 'required';
        }

        $validated = $request->validate($rules);

        // Verifikasi password saat ini
        if (
            $targetUser->user_id === $currentUser->user_id &&
            !Hash::check($validated['current_password'], $targetUser->password)
        ) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        // Update password
        $targetUser->password = Hash::make($validated['new_password']);
        $targetUser->save();

        // Redirect dengan pesan sukses
        return redirect()->route('account-management.change-password', ['user' => $targetUser->user_id])
            ->with('status', 'Password berhasil diubah!');
    }
}
