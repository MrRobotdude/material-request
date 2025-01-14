<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            ['brand_name' => 'Samsung', 'brand_initial' => 'SSG'],
            ['brand_name' => 'Apple', 'brand_initial' => 'APL'],
            ['brand_name' => 'Sony', 'brand_initial' => 'SNY'],
            ['brand_name' => 'LG', 'brand_initial' => 'LG'],
            ['brand_name' => 'Huawei', 'brand_initial' => 'HW'],
            ['brand_name' => 'Xiaomi', 'brand_initial' => 'MI'],
            ['brand_name' => 'Oppo', 'brand_initial' => 'OP'],
            ['brand_name' => 'Dell', 'brand_initial' => 'DEL'],
            ['brand_name' => 'Asus', 'brand_initial' => 'ASU'],
            ['brand_name' => 'HP', 'brand_initial' => 'HPQ'],
            ['brand_name' => 'Acer', 'brand_initial' => 'ACR'],
        ];

        foreach ($brands as $brandData) {
            Brand::create([
                'brand_code' => Brand::generateBrandCode(),
                'brand_name' => $brandData['brand_name'],
                'brand_initial' => $brandData['brand_initial'],
                'is_active' => 1,
            ]);
        }
    }
}
