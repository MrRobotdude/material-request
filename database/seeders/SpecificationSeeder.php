<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specification;

class SpecificationSeeder extends Seeder
{
    public function run()
    {
        $specifications = [
            // Type specifications (without unit)
            ['specification_name' => 'Type', 'unit' => null],
            ['specification_name' => 'Processor Speed', 'unit' => 'GHz'],
            ['specification_name' => 'RAM', 'unit' => 'GB'],
            ['specification_name' => 'Storage Capacity', 'unit' => 'TB'],
            ['specification_name' => 'Screen Size', 'unit' => 'Inch'],
            ['specification_name' => 'Battery Capacity', 'unit' => 'mAh'],
            ['specification_name' => 'Weight', 'unit' => 'Kg'],
            ['specification_name' => 'Resolution', 'unit' => 'Pixels'],
            ['specification_name' => 'Graphics Memory', 'unit' => 'GB'],
            ['specification_name' => 'Refresh Rate', 'unit' => 'Hz'],
            ['specification_name' => 'Power Supply', 'unit' => 'Watt'],
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
