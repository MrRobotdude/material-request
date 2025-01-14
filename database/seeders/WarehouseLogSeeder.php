<?php

namespace Database\Seeders;

use App\Models\WarehouseLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseLogSeeder extends Seeder
{
    public function run()
    {
        DB::table('warehouse_logs')->insert([
            [
                'mr_item_id' => 4,
                'fulfilled_quantity' => 1,
                'remaining_quantity' => 4,
                'created_at' => now(),
            ],
            [
                'mr_item_id' => 5,
                'fulfilled_quantity' => 3,
                'remaining_quantity' => 0,
                'created_at' => now(),
            ],
        ]);
    }
}
