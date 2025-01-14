<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = true;

    /**
     * Primary key untuk model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indikasi apakah primary key bersifat auto-increment.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function generateUserId($joinDate)
    {
        // Format tanggal (DDMMYY)
        $datePart = $joinDate->format('dmy');

        // Hitung jumlah karyawan yang bergabung pada hari tersebut
        $count = self::whereDate('created_at', $joinDate->format('Y-m-d'))->count() + 1;

        // Format angka ke 3 digit (XXX)
        $countPart = str_pad($count, 3, '0', STR_PAD_LEFT);

        // Gabungkan menjadi format XXXDDMMYY
        return $countPart . $datePart;
    }


    /**
     * Relasi ke model Role.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')->withTimestamps();
    }


    /**
     * Periksa apakah user memiliki permission tertentu.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permissions)
    {
        $rolesWithPermissions = $this->roles()->with('permissions')->get();

        // Ambil semua izin pengguna
        $allPermissions = $rolesWithPermissions->pluck('permissions.*.name')->flatten();

        // Ambil grup izin akses penuh dari konfigurasi
        $permissionGroups = config('permissions');

        // 1. Cek Akses Penuh Berdasarkan Grup
        foreach ($permissionGroups as $fullAccessKey => $relatedPermissions) {
            if ($allPermissions->contains($fullAccessKey)) {
                // Jika memiliki akses penuh, izinkan semua izin dalam grup
                if (is_array($permissions)) {
                    if (!empty(array_intersect($permissions, $relatedPermissions))) {
                        return true;
                    }
                } elseif (in_array($permissions, $relatedPermissions)) {
                    return true;
                }
            }
        }

        // 2. Cek Izin Spesifik Jika Tidak Ada Akses Penuh
        if (is_array($permissions)) {
            $result = $allPermissions->intersect($permissions)->isNotEmpty();
            return $result;
        }

        $result = $allPermissions->contains($permissions);
        return $result;
    }



    /**
     * Periksa apakah user memiliki role tertentu.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }
}

