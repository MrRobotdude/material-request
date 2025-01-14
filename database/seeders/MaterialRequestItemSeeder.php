<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialRequestItemSeeder extends Seeder
{
    public function run()
    {
        $mrRequests = DB::table('material_requests')->get();
        $items = DB::table('items')->where('is_active', 1)->pluck('item_id'); // Hanya ambil item aktif
        $statusesForPartial = ['pending', 'partial', 'fulfilled', 'cancelled'];

        foreach ($mrRequests as $mr) {
            $numItems = rand(1, 10); // Setiap MR memiliki 1-10 item
            $status = $mr->status;

            for ($i = 1; $i <= $numItems; $i++) {
                $quantity = rand(1, 10); // Jumlah total item
                $fulfilledQuantity = 0; // Default fulfilled quantity
                $itemStatus = 'pending'; // Default status

                // Tentukan status dan fulfilled_quantity berdasarkan status MR
                if ($status === 'completed') {
                    $itemStatus = 'fulfilled';
                    $fulfilledQuantity = $quantity;
                } elseif ($status === 'cancelled') {
                    $itemStatus = 'cancelled';
                    $fulfilledQuantity = rand(0, $quantity - 1);
                } elseif ($status === 'partial') {
                    $itemStatus = $statusesForPartial[array_rand($statusesForPartial)];
                    if ($itemStatus === 'partial') {
                        $fulfilledQuantity = rand(1, $quantity - 1);
                    } elseif ($itemStatus === 'fulfilled') {
                        $fulfilledQuantity = $quantity;
                    } elseif ($itemStatus === 'cancelled') {
                        $fulfilledQuantity = rand(0, $quantity - 1);
                    } elseif ($itemStatus === 'pending') {
                        $fulfilledQuantity = 0;
                    }
                } elseif ($status === 'pending' || $status === 'approved') {
                    $itemStatus = 'pending';
                    $fulfilledQuantity = 0;
                }

                // Pilih item_id berdasarkan kondisi status MR
                $itemId = ($status === 'completed' || $status === 'cancelled')
                    ? DB::table('items')->inRandomOrder()->first()->item_id // Pilih item acak untuk status ini
                    : $items->random(); // Pilih item aktif lainnya

                // Masukkan data ke tabel material_request_items
                DB::table('material_request_items')->insert([
                    'mr_code' => $mr->mr_code,
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                    'fulfilled_quantity' => $fulfilledQuantity,
                    'status' => $itemStatus,
                    'created_at' => $mr->created_at,
                    'updated_at' => $mr->created_at,
                ]);
            }
        }
    }
}

