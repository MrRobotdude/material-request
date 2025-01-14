<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Type;
use App\Models\SubType;

class SubTypeSeeder extends Seeder
{
    public function run()
    {
        $subTypes = [
            // SubType untuk Electronics
            'Electronics' => [
                ['sub_type_name' => 'Laptops', 'initial' => 'LAP'],
                ['sub_type_name' => 'Smartphones', 'initial' => 'SMP'],
                ['sub_type_name' => 'Tablets', 'initial' => 'TAB'],
            ],
            // SubType untuk Furniture
            'Furniture' => [
                ['sub_type_name' => 'Chairs', 'initial' => 'CHR'],
                ['sub_type_name' => 'Tables', 'initial' => 'TBL'],
                ['sub_type_name' => 'Cabinets', 'initial' => 'CAB'],
            ],
            // SubType untuk Stationery
            'Stationery' => [
                ['sub_type_name' => 'Pens', 'initial' => 'PEN'],
                ['sub_type_name' => 'Notebooks', 'initial' => 'NTB'],
                ['sub_type_name' => 'Folders', 'initial' => 'FLD'],
            ],
        ];

        foreach ($subTypes as $typeName => $subTypeData) {
            $type = Type::where('type_name', $typeName)->first();
            if ($type) {
                foreach ($subTypeData as $subType) {
                    $subTypeCode = SubType::generateSubTypeCode();
                    SubType::create([
                        'sub_type_code' => $subTypeCode,
                        'sub_type_name' => $subType['sub_type_name'],
                        'initial' => $subType['initial'],
                        'type_code' => $type->type_code,
                        'is_active' => 1,
                    ]);
                }
            }
        }
    }
}
