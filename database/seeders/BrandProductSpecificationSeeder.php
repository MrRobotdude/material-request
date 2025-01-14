<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Specification;

class BrandProductSpecificationSeeder extends Seeder
{
    public function run()
    {
        $brands = Brand::all();
        $products = Product::all();
        $specifications = Specification::all();

        // Ambil spesifikasi dengan nama "Type"
        $typeSpecification = Specification::where('specification_name', 'Type')->first();

        if (!$typeSpecification) {
            $this->command->error('Specification "Type" not found. Please check your SpecificationSeeder.');
            return;
        }

        foreach ($brands as $brand) {
            $brandProducts = $products->random(3); // Pilih 3 produk acak untuk setiap brand

            foreach ($brandProducts as $product) {
                // Hubungkan spesifikasi "Type"
                \DB::table('brand_product_specification')->insert([
                    'brand_code' => $brand->brand_code,
                    'product_code' => $product->product_code,
                    'specification_id' => $typeSpecification->specification_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Hubungkan spesifikasi acak lainnya
                $randomSpecifications = $specifications->where('specification_id', '!=', $typeSpecification->specification_id)->random(3);

                foreach ($randomSpecifications as $specification) {
                    \DB::table('brand_product_specification')->insert([
                        'brand_code' => $brand->brand_code,
                        'product_code' => $product->product_code,
                        'specification_id' => $specification->specification_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
