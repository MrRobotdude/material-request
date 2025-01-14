<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCanChangePassword
{
    public function handle(Request $request, Closure $next)
    {
        $authUser = $request->user(); // Pengguna yang sedang login
        $targetUser = $request->route('user'); // Pengguna yang ditarget

        // Jika target user adalah string, resolve menjadi model User
        if (is_string($targetUser)) {
            $targetUser = \App\Models\User::findOrFail($targetUser);
        }

        // Jika pengguna tidak memiliki permission `change_password`, hanya bisa mengganti password mereka sendiri
        if (!$authUser->can('change_password') && $authUser->user_id !== $targetUser->user_id) {
            return redirect()->route('account-management.index')
                ->withErrors(['error' => 'Anda tidak dapat mengganti password akun selain akun sendiri.']);
        }

        return $next($request);
    }
}
