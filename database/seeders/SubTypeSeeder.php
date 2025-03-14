<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;
use App\Models\SubType;
use Illuminate\Support\Str;

class SubTypeSeeder extends Seeder
{
    public function run()
    {
        $subTypes = [
            // 🔹 Cooling Equipment (Kategori Lebih Umum, Tapi Lebih Spesifik dari Type)
            'Cooling Equipment' => [
                ['sub_type_name' => 'Split AC', 'initial' => 'SPAC'],
                ['sub_type_name' => 'VRF System', 'initial' => 'VRF'],
                ['sub_type_name' => 'Chiller Water Cooled', 'initial' => 'CHLW'],
                ['sub_type_name' => 'Chiller Air Cooled', 'initial' => 'CHL'],
                ['sub_type_name' => 'Cooling Tower', 'initial' => 'CT'],
                ['sub_type_name' => 'Fan Coil Unit (FCU)', 'initial' => 'FCU'],
            ],

            // 🔹 Heating Equipment
            'Heating Equipment' => [
                ['sub_type_name' => 'Duct Heater', 'initial' => 'DH'],
                ['sub_type_name' => 'Boiler Steam', 'initial' => 'BS'],
                ['sub_type_name' => 'Heat Exchanger', 'initial' => 'HE'],
            ],

            // 🔹 Ventilation Equipment
            'Ventilation Equipment' => [
                ['sub_type_name' => 'Exhaust Fan', 'initial' => 'EF'],
                ['sub_type_name' => 'Ducting Spiral', 'initial' => 'DS'],
                ['sub_type_name' => 'HEPA Filter', 'initial' => 'HEPA'],
            ],

            // 🔹 Control & Automation
            'Control & Automation' => [
                ['sub_type_name' => 'Thermostat', 'initial' => 'THS'],
                ['sub_type_name' => 'Variable Air Volume (VAV) Box', 'initial' => 'VAV'],
            ],

            // 🔹 Accessories & Consumables
            'Accessories & Consumables' => [
                ['sub_type_name' => 'Refrigeran (Freon)', 'initial' => 'RFN'],
                ['sub_type_name' => 'Insulasi Pipa', 'initial' => 'INS'],
                ['sub_type_name' => 'Silencer (Peredam Suara)', 'initial' => 'SLR'],
            ],
        ];

        foreach ($subTypes as $typeName => $subTypeData) {
            $type = Type::where('type_name', $typeName)->first();

            if ($type) {
                foreach ($subTypeData as $subType) {
                    SubType::create([
                        'sub_type_code' => method_exists(SubType::class, 'generateSubTypeCode')
                            ? SubType::generateSubTypeCode()
                            : Str::random(6),
                        'sub_type_name' => $subType['sub_type_name'],
                        'initial' => $subType['initial'],
                        'type_code' => $type->type_code,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
