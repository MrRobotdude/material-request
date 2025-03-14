<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // 🔹 Cooling Equipment
            ['product_name' => 'Air Conditioner Split', 'initial' => 'ACS'],
            ['product_name' => 'VRF Outdoor Unit', 'initial' => 'VRFODU'],
            ['product_name' => 'VRF Indoor Cassette', 'initial' => 'VRFINC'],
            ['product_name' => 'Chiller Water Cooled', 'initial' => 'CHLWC'],
            ['product_name' => 'Chiller Air Cooled', 'initial' => 'CHLAC'],
            ['product_name' => 'Cooling Tower', 'initial' => 'CT'],
            ['product_name' => 'Fan Coil Unit', 'initial' => 'FCU'],

            // 🔹 Heating Equipment
            ['product_name' => 'Duct Heater', 'initial' => 'DH'],
            ['product_name' => 'Boiler Steam', 'initial' => 'BS'],
            ['product_name' => 'Heat Exchanger', 'initial' => 'HE'],

            // 🔹 Ventilation Equipment
            ['product_name' => 'Exhaust Fan', 'initial' => 'EF'],
            ['product_name' => 'Ducting', 'initial' => 'DCT'],
            ['product_name' => 'HEPA Filter', 'initial' => 'HEPA'],

            // 🔹 Control & Automation
            ['product_name' => 'Thermostat', 'initial' => 'THS'],
            ['product_name' => 'Variable Air Volume (VAV) Box', 'initial' => 'VAV'],

            // 🔹 Accessories & Consumables
            ['product_name' => 'Refrigeran (Freon)', 'initial' => 'RFN'],
            ['product_name' => 'Insulasi Pipa', 'initial' => 'INS'],
            ['product_name' => 'Silencer (Peredam Suara)', 'initial' => 'SLR'],
        ];

        foreach ($products as $productData) {
            Product::create([
                'product_code' => method_exists(Product::class, 'generateProductCode')
                    ? Product::generateProductCode()
                    : Str::random(6),
                'product_name' => $productData['product_name'],
                'product_initial' => $productData['initial'],
                'is_active' => true,
            ]);
        }
    }
}
