<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $joinDate = Carbon::today();

        $users = [
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Purchasing User',
                'username' => 'purchasing',
                'password' => Hash::make('password123'),
                'role' => 'purchasing',
            ],
            [
                'name' => 'Operational User',
                'username' => 'operational',
                'password' => Hash::make('password123'),
                'role' => 'operational',
            ],
            [
                'name' => 'Marketing User',
                'username' => 'marketing',
                'password' => Hash::make('password123'),
                'role' => 'marketing',
            ],
            [
                'name' => 'Warehouse User',
                'username' => 'warehouse',
                'password' => Hash::make('password123'),
                'role' => 'warehouse',
            ],
            [
                'name' => 'Finance User',
                'username' => 'finance',
                'password' => Hash::make('password123'),
                'role' => 'finance',
            ],
            [
                'name' => 'User',
                'username' => 'user',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ],
        ];

        foreach ($users as $userData) {
            $role = Role::where('name', $userData['role'])->first();
            unset($userData['role']);

            $userData['user_id'] = User::generateUserId($joinDate); // Buat user_id

            $user = User::create($userData);
            $user->roles()->attach($role);
        }
    }
}

