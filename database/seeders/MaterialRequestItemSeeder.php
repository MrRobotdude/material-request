<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
                    ? DB::table('items')->inRandomOrder()->first()->item_id
                    : $items->random();

                // **Menentukan harga berdasarkan item (Menyesuaikan harga HVAC yang realistis)**
                $price = $this->getPriceForItem($itemId);

                // Masukkan data ke tabel material_request_items
                $mrItemId = DB::table('material_request_items')->insertGetId([
                    'mr_code' => $mr->mr_code,
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                    'fulfilled_quantity' => $fulfilledQuantity,
                    'status' => $itemStatus,
                    'price' => $price,
                    'created_at' => $mr->created_at,
                    'updated_at' => $mr->created_at,
                ]);

                // **Tambahkan ke warehouse_logs jika fulfilled_quantity > 0**
                if ($fulfilledQuantity > 0) {
                    $randomDays = rand(0, 5); // Ambil angka random 0-5
                    $randomCreatedAt = Carbon::parse($mr->created_at)->addDays($randomDays); // Tambahkan ke tanggal MR

                    DB::table('warehouse_logs')->insert([
                        'mr_item_id' => $mrItemId,
                        'fulfilled_quantity' => $fulfilledQuantity,
                        'remaining_quantity' => $quantity - $fulfilledQuantity,
                        'created_at' => $randomCreatedAt,
                    ]);
                }
            }
        }
    }

    /**
     * Fungsi untuk menentukan harga berdasarkan item_id dalam industri HVAC.
     */
    private function getPriceForItem($itemId)
    {
        $item = DB::table('items')->where('item_id', $itemId)->first();

        if (!$item) {
            return intval(rand(500000, 5000000) / 1000) * 1000;
        }

        $itemName = strtolower($item->description);

        if (strpos($itemName, 'air conditioner') !== false || strpos($itemName, 'chiller') !== false) {
            return intval(rand(5000000, 50000000) / 1000) * 1000;
        } elseif (strpos($itemName, 'cooling tower') !== false) {
            return intval(rand(10000000, 150000000) / 1000) * 1000;
        } elseif (strpos($itemName, 'ducting') !== false || strpos($itemName, 'ventilation') !== false) {
            return intval(rand(50000, 5000000) / 1000) * 1000;
        } elseif (strpos($itemName, 'thermostat') !== false || strpos($itemName, 'controller') !== false) {
            return intval(rand(100000, 3000000) / 1000) * 1000;
        } elseif (strpos($itemName, 'refrigeran') !== false) {
            return intval(rand(500000, 5000000) / 1000) * 1000;
        } elseif (strpos($itemName, 'boiler') !== false || strpos($itemName, 'heater') !== false) {
            return intval(rand(2000000, 20000000) / 1000) * 1000;
        } elseif (strpos($itemName, 'hepa filter') !== false) {
            return intval(rand(1000000, 10000000) / 1000) * 1000;
        } elseif (strpos($itemName, 'fan coil') !== false) {
            return intval(rand(2000000, 10000000) / 1000) * 1000;
        } elseif (strpos($itemName, 'insulasi') !== false) {
            return intval(rand(100000, 2000000) / 1000) * 1000;
        } else {
            return intval(rand(500000, 5000000) / 1000) * 1000;
        }
    }
}
