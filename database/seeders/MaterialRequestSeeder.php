<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialRequestSeeder extends Seeder
{
    public function run()
    {
        $users = DB::table('users')->pluck('user_id', 'username');
        $projects = DB::table('projects')->pluck('project_id'); // Ambil semua project_id
        $statuses = ['created', 'approved', 'partial', 'completed', 'cancelled'];
        $now = Carbon::now();

        for ($i = 1; $i <= 200; $i++) {
            $status = $statuses[array_rand($statuses)]; // Pilih status secara acak
            $createdAt = $now->copy()->subDays(rand(0, 100)); // Tanggal dibuat acak dalam 1 tahun terakhir
            $createdBy = $users->random(); // Pilih user acak
            $projectId = $projects->random(); // Pilih project acak

            DB::table('material_requests')->insert([
                'mr_code' => 'MR-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'project_id' => $projectId,
                'note' => 'Material Request ke-' . $i,
                'created_by' => $createdBy,
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
