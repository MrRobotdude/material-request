<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Specification;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['product_name' => 'Laptop', 'product_initial' => 'LTP'],
            ['product_name' => 'Smartphone', 'product_initial' => 'SPH'],
            ['product_name' => 'Tablet', 'product_initial' => 'TBL'],
            ['product_name' => 'Smartwatch', 'product_initial' => 'SW'],
            ['product_name' => 'Monitor', 'product_initial' => 'MNT'],
            ['product_name' => 'Keyboard', 'product_initial' => 'KB'],
            ['product_name' => 'Mouse', 'product_initial' => 'MS'],
            ['product_name' => 'Gaming Laptop', 'product_initial' => 'GLP'],
            ['product_name' => 'Ultrabook', 'product_initial' => 'ULB'],
            ['product_name' => 'Graphics Tablet', 'product_initial' => 'GFXT'],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'product_code' => Product::generateProductCode(),
                'product_name' => $productData['product_name'],
                'product_initial' => $productData['product_initial'],
                'is_active' => 1,
            ]);
        }
    }
}
