<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;
use Illuminate\Support\Str;

class TypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            // 🔹 Kategori Utama dalam Industri HVAC
            ['type_name' => 'Cooling Equipment', 'initial' => 'COOL'], // Pendinginan
            ['type_name' => 'Heating Equipment', 'initial' => 'HEAT'], // Pemanasan
            ['type_name' => 'Ventilation Equipment', 'initial' => 'VENT'], // Ventilasi
            ['type_name' => 'Control & Automation', 'initial' => 'CTRL'], // Sistem Kontrol HVAC
            ['type_name' => 'Accessories & Consumables', 'initial' => 'ACCS'], // Aksesori & Material Pendukung
        ];

        foreach ($types as $typeData) {
            Type::create([
                'type_code' => method_exists(Type::class, 'generateTypeCode')
                    ? Type::generateTypeCode()
                    : Str::random(6), // Fallback jika method tidak ada
                'type_name' => $typeData['type_name'],
                'initial' => $typeData['initial'],
                'is_active' => true,
            ]);
        }
    }
}
