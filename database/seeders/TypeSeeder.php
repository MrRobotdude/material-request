<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;

class TypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['type_name' => 'Electronics', 'initial' => 'ELEC'],
            ['type_name' => 'Furniture', 'initial' => 'FURN'],
            ['type_name' => 'Stationery', 'initial' => 'STAT'],
        ];

        foreach ($types as $typeData) {
            Type::create([
                'type_code' => Type::generateTypeCode(),
                'type_name' => $typeData['type_name'],
                'initial' => $typeData['initial'],
                'is_active' => 1,
            ]);
        }
    }
}
