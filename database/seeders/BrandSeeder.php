<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            ['brand_name' => 'Daikin', 'brand_initial' => 'DKN'],
            ['brand_name' => 'Mitsubishi Electric', 'brand_initial' => 'MEL'],
            ['brand_name' => 'Carrier', 'brand_initial' => 'CRR'],
            ['brand_name' => 'Trane', 'brand_initial' => 'TRN'],
            ['brand_name' => 'Panasonic', 'brand_initial' => 'PNC'],
            ['brand_name' => 'Toshiba', 'brand_initial' => 'TSB'],
            ['brand_name' => 'York', 'brand_initial' => 'YRK'],
            ['brand_name' => 'Hitachi', 'brand_initial' => 'HTC'],
            ['brand_name' => 'LG', 'brand_initial' => 'LG'],
            ['brand_name' => 'Fujitsu', 'brand_initial' => 'FJT'],
        ];

        foreach ($brands as $brandData) {
            Brand::create([
                'brand_code' => method_exists(Brand::class, 'generateBrandCode')
                    ? Brand::generateBrandCode()
                    : Str::random(6), // Fallback jika method tidak ada
                'brand_name' => $brandData['brand_name'],
                'brand_initial' => $brandData['brand_initial'],
                'is_active' => true,
            ]);
        }
    }
}
