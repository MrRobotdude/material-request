<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RolePermissionMiddleware
{
    public function handle($request, Closure $next, $permissions)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        // Pecah izin menjadi array
        $permissionsArray = explode(',', $permissions);

        // Ambil semua izin pengguna
        $userPermissions = $user->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions.*.name')
            ->flatten();

        // Ambil grup izin akses penuh dari konfigurasi
        $permissionGroups = config('permissions');

        foreach ($permissionsArray as $permission) {
            $trimmedPermission = trim($permission);

            // Cek jika pengguna memiliki akses penuh untuk grup terkait
            foreach ($permissionGroups as $fullAccessKey => $relatedPermissions) {
                if ($userPermissions->contains($fullAccessKey) && in_array($trimmedPermission, $relatedPermissions)) {
                    return $next($request); // Berikan akses penuh jika memiliki izin akses penuh
                }
            }

            // Cek izin spesifik
            if ($userPermissions->contains($trimmedPermission)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access');
    }
}

