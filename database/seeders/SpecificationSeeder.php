<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specification;

class SpecificationSeeder extends Seeder
{
    public function run()
    {
        $specifications = [
            // 🔹 General Specification (Tanpa Unit)
            ['specification_name' => 'Type', 'unit' => null],

            // 🔹 Cooling Equipment
            ['specification_name' => 'Kapasitas Pendinginan', 'unit' => 'PK'],
            ['specification_name' => 'Kapasitas Pendinginan', 'unit' => 'HP'],
            ['specification_name' => 'Kapasitas Pendinginan', 'unit' => 'TR'],
            ['specification_name' => 'Daya Listrik', 'unit' => 'Watt'],
            ['specification_name' => 'Sumber Daya', 'unit' => 'Volt'],
            ['specification_name' => 'Refrigeran', 'unit' => null],
            ['specification_name' => 'Kapasitas Air Flow', 'unit' => 'CFM'],
            ['specification_name' => 'Tekanan Refrigeran', 'unit' => 'Bar'],
            ['specification_name' => 'Efisiensi Energi', 'unit' => '%'],
            ['specification_name' => 'Tingkat Kebisingan', 'unit' => 'dB'],

            // 🔹 Heating Equipment
            ['specification_name' => 'Kapasitas Pemanasan', 'unit' => 'kW'],
            ['specification_name' => 'Tekanan Steam', 'unit' => 'Bar'],
            ['specification_name' => 'Daya Pemanas', 'unit' => 'kW'],
            ['specification_name' => 'Efisiensi Termal', 'unit' => '%'],
            ['specification_name' => 'Suhu Operasional', 'unit' => '°C'],
            ['specification_name' => 'Tipe Bahan Bakar', 'unit' => null],

            // 🔹 Ventilation Equipment
            ['specification_name' => 'Air Flow', 'unit' => 'CFM'],
            ['specification_name' => 'Tekanan Statis', 'unit' => 'Pa'],
            ['specification_name' => 'Kecepatan Putaran', 'unit' => 'RPM'],
            ['specification_name' => 'Diameter Ducting', 'unit' => 'mm'],
            ['specification_name' => 'Tingkat Kebisingan', 'unit' => 'dB'],
            ['specification_name' => 'Material Ducting', 'unit' => null],

            // 🔹 Control & Automation
            ['specification_name' => 'Tipe Kontrol', 'unit' => null],
            ['specification_name' => 'Tegangan Operasional', 'unit' => 'Volt'],
            ['specification_name' => 'Wi-Fi Compatible', 'unit' => null],
            ['specification_name' => 'Protokol Komunikasi', 'unit' => null],
            ['specification_name' => 'Sensor Suhu', 'unit' => '°C'],
            ['specification_name' => 'Kecepatan Respon', 'unit' => 'ms'],

            // 🔹 Accessories & Consumables
            ['specification_name' => 'Ketebalan Insulasi', 'unit' => 'mm'],
            ['specification_name' => 'Tipe Silencer', 'unit' => null],
            ['specification_name' => 'Kapasitas Pompa Drain', 'unit' => 'L/h'],
            ['specification_name' => 'Tipe Refrigeran', 'unit' => null],
            ['specification_name' => 'Tingkat Konduktivitas Termal', 'unit' => 'W/mK'],
            ['specification_name' => 'Daya Tahan terhadap Suhu', 'unit' => '°C'],
            ['specification_name' => 'Tingkat Penyerapan Suara', 'unit' => 'dB'],
        ];

        foreach ($specifications as $specificationData) {
            Specification::create([
                'specification_name' => $specificationData['specification_name'],
                'unit' => $specificationData['unit'],
                'is_active' => 1,
            ]);
        }
    }
}
