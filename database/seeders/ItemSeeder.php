<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Type;
use App\Models\SubType;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $brands = Brand::where('is_active', 1)->get();
        $products = Product::where('is_active', 1)->get();
        $subTypes = SubType::with('type')->where('is_active', 1)->get();

        if ($brands->isEmpty() || $products->isEmpty() || $subTypes->isEmpty()) {
            $this->command->error('Brands, Products, or SubTypes table is empty. Please seed these tables first.');
            return;
        }

        foreach ($brands as $brand) {
            foreach ($products as $product) {
                // Cek apakah pasangan brand dan product ada di tabel brand_product_specification
                $specifications = $this->getSpecifications($brand->brand_code, $product->product_code);

                if ($specifications->isEmpty()) {
                    continue; // Lewati jika tidak ada pasangan
                }

                foreach ($subTypes as $subType) {
                    // Generate deskripsi spesifikasi
                    $specDescription = $this->generateSpecificationDescription($specifications);

                    // Generate unique item_code
                    $itemCode = $this->generateItemCode($subType->type, $subType);

                    // Generate deskripsi lengkap
                    $description = "Product: {$product->product_name}, Brand: {$brand->brand_name}";
                    if (!empty($specDescription)) {
                        $description .= ", " . $specDescription;
                    }

                    // Insert item
                    Item::create([
                        'brand_code' => $brand->brand_code,
                        'product_code' => $product->product_code,
                        'type_code' => $subType->type_code,
                        'sub_type_code' => $subType->sub_type_code,
                        'item_code' => $itemCode,
                        'description' => $description,
                        'unit' => $this->generateUnit($subType->type),
                        'is_active' => rand(0, 1), // Random active/inactive
                    ]);
                }
            }
        }
    }

    private function getSpecifications($brandCode, $productCode)
    {
        // Ambil spesifikasi dari tabel brand_product_specification
        return DB::table('brand_product_specification')
            ->join('specifications', 'brand_product_specification.specification_id', '=', 'specifications.specification_id')
            ->where('brand_product_specification.brand_code', $brandCode)
            ->where('brand_product_specification.product_code', $productCode)
            ->select('specifications.specification_name', 'specifications.unit')
            ->get();
    }

    private function generateSpecificationDescription($specifications)
    {
        // Buat deskripsi spesifikasi
        $description = [];
        foreach ($specifications as $spec) {
            $unit = $spec->unit ? "({$spec->unit})" : "";
            $value = $this->generateRandomValue($spec->specification_name, $spec->unit);
            $description[] = "{$spec->specification_name}{$unit}: {$value}";
        }

        return implode(', ', $description);
    }

    private function generateRandomValue($specName, $unit)
    {
        switch ($unit) {
            case 'GHz':
                return rand(2, 5) . "." . rand(0, 9); // Contoh: 2.3 GHz
            case 'GB':
                return rand(4, 64); // Contoh: 16 GB
            case 'TB':
                return rand(1, 10); // Contoh: 1 TB
            case 'Inch':
                return rand(10, 50); // Contoh: 14 Inch
            case 'mAh':
                return rand(2000, 10000); // Contoh: 5000 mAh
            case 'Kg':
                return rand(1, 10) . "." . rand(0, 9); // Contoh: 2.5 Kg
            case 'Pixels':
                return rand(1000, 4000) . "x" . rand(1000, 4000); // Contoh: 1920x1080 Pixels
            case 'Hz':
                return rand(50, 240); // Contoh: 120 Hz
            case 'Watt':
                return rand(50, 1000); // Contoh: 500 Watt
            default:
                return rand(1, 100); // Default random value
        }
    }

    private function generateItemCode($type, $subType)
    {
        // Ambil initial dari type dan subtype
        $typeInitial = Str::upper($type->initial);
        $subTypeInitial = Str::upper($subType->initial);

        // Ambil ID item terakhir untuk penomoran
        $latestItemId = Item::max('item_id') ?? 0;
        $nextItemId = $latestItemId + 1;

        // Format item_code
        return "{$typeInitial}-{$subTypeInitial}-" . str_pad($nextItemId, 5, '0', STR_PAD_LEFT);
    }

    private function generateUnit($type)
    {
        $defaultUnits = [
            'Tablet' => 'UNIT',
            'Cable' => 'ROL',
            'Paper' => 'RIM',
            'Default' => 'PCS',
        ];

        return $defaultUnits[$type->type_name] ?? $defaultUnits['Default'];
    }
}
