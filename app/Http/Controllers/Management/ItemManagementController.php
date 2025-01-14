<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Type;
use App\Models\SubType;
use App\Models\Item;
use App\Models\Specification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemManagementController extends Controller
{
    public function index()
    {
        $items = Item::with(['brand', 'product', 'type', 'subType'])->get();
        return view('pages.item-management.index', compact('items'));
    }

    public function create()
    {
        $brands = Brand::where('is_active', 1)->get();
        $products = Product::where('is_active', 1)->get();
        $types = Type::where('is_active', 1)->get();
        $subTypes = SubType::where('is_active', 1)->get();

        $brandProductRelations = DB::table('brand_product_specification')
            ->where('is_active', 1)
            ->select('brand_code', 'product_code', 'specification_id')
            ->distinct()
            ->get();

        $specifications = Specification::where('is_active', 1)->get();

        return view('pages.item-management.form', compact(
            'brands',
            'products',
            'types',
            'subTypes',
            'brandProductRelations',
            'specifications'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_code' => 'required|exists:brands,brand_code',
            'product_code' => 'required|exists:products,product_code',
            'type_code' => 'required|exists:types,type_code',
            'sub_type_code' => 'required|exists:sub_types,sub_type_code',
            'unit' => 'required|string|max:10',
            'specifications' => 'required|array',
            'specifications.*' => 'required|string',
        ]);

        $latestItemId = Item::max('item_id') ?? 0;
        $nextItemId = $latestItemId + 1;

        $type = Type::where('type_code', $validated['type_code'])->first();
        $subType = SubType::where('sub_type_code', $validated['sub_type_code'])->first();

        $itemCode = strtoupper($type->initial) . '-' . strtoupper($subType->initial) . '-' . str_pad($nextItemId, 5, '0', STR_PAD_LEFT);

        // Fetch Brand and Product Names
        $brandName = Brand::where('brand_code', $validated['brand_code'])->value('brand_name');
        $productName = Product::where('product_code', $validated['product_code'])->value('product_name');

        // Generate description from specifications
        $descriptionParts = [
            "Product: $productName",
            "Brand: $brandName"
        ];

        foreach ($validated['specifications'] as $key => $value) {
            $specification = Specification::find($key);
            if ($specification) {
                $unit = $specification->unit ? " ({$specification->unit})" : "";
                $descriptionParts[] = "{$specification->specification_name}{$unit}: $value";
            }
        }
        $description = implode(', ', $descriptionParts);

        // Save item
        Item::create([
            'brand_code' => $validated['brand_code'],
            'product_code' => $validated['product_code'],
            'type_code' => $validated['type_code'],
            'sub_type_code' => $validated['sub_type_code'],
            'item_code' => $itemCode,
            'description' => $description,
            'unit' => $validated['unit'],
            'is_active' => 1,
        ]);

        return redirect()->route('item-management.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $brands = Brand::where('is_active', 1)->get();
        $products = Product::where('is_active', 1)->get();
        $types = Type::where('is_active', 1)->get();
        $subTypes = SubType::where('is_active', 1)->get();

        // Ambil spesifikasi terkait dari description
        $relatedSpecifications = DB::table('brand_product_specification')
            ->join('specifications', 'brand_product_specification.specification_id', '=', 'specifications.specification_id')
            ->where('brand_product_specification.brand_code', $item->brand_code)
            ->where('brand_product_specification.product_code', $item->product_code)
            ->select('specifications.specification_id', 'specifications.specification_name', 'specifications.unit')
            ->get();

        $specifications = [];
        foreach ($relatedSpecifications as $spec) {
            $pattern = sprintf('/%s\s?(\((.*?)\))?:\s?(.*?)(,|$)/', preg_quote($spec->specification_name));
            if (preg_match($pattern, $item->description, $matches)) {
                $specifications[] = [
                    'specification_id' => $spec->specification_id,
                    'specification_name' => $spec->specification_name,
                    'unit' => $spec->unit,
                    'value' => trim($matches[3]),
                ];
            } else {
                $specifications[] = [
                    'specification_id' => $spec->specification_id,
                    'specification_name' => $spec->specification_name,
                    'unit' => $spec->unit,
                    'value' => '',
                ];
            }
        }

        $brandProductRelations = DB::table('brand_product_specification')
            ->where('is_active', 1)
            ->select('brand_code', 'product_code', 'specification_id')
            ->distinct()
            ->get();

        return view('pages.item-management.form', compact(
            'item',
            'brands',
            'products',
            'types',
            'subTypes',
            'specifications',
            'brandProductRelations'
        ));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'brand_code' => 'required|exists:brands,brand_code',
            'product_code' => 'required|exists:products,product_code',
            'type_code' => 'required|exists:types,type_code',
            'sub_type_code' => 'required|exists:sub_types,sub_type_code',
            'unit' => 'required|string|max:10',
            'specifications' => 'required|array',
            'specifications.*' => 'required|string',
        ]);

        // Fetch Brand and Product Names
        $brandName = Brand::where('brand_code', $validated['brand_code'])->value('brand_name');
        $productName = Product::where('product_code', $validated['product_code'])->value('product_name');

        // Generate description from specifications
        $descriptionParts = [
            "Product: $productName",
            "Brand: $brandName"
        ];

        foreach ($validated['specifications'] as $key => $value) {
            $specification = Specification::find($key);
            if ($specification) {
                $unit = $specification->unit ? " ({$specification->unit})" : "";
                $descriptionParts[] = "{$specification->specification_name}{$unit}: $value";
            }
        }
        $description = implode(', ', $descriptionParts);

        $item->update([
            'brand_code' => $validated['brand_code'],
            'product_code' => $validated['product_code'],
            'type_code' => $validated['type_code'],
            'sub_type_code' => $validated['sub_type_code'],
            'description' => $description,
            'unit' => $validated['unit'],
        ]);

        return redirect()->route('item-management.index')->with('success', 'Item berhasil diperbarui.');
    }

    public function toggleStatus(Item $item)
    {
        // Cek apakah item memiliki material request item yang statusnya bukan completed atau canceled
        $hasPendingMR = $item->materialRequestItems()
            ->whereNotIn('status', ['completed', 'canceled'])
            ->exists();

        // Jika ada material request item yang belum selesai, jangan izinkan dinonaktifkan
        if ($item->is_active && $hasPendingMR) {
            return redirect()->route('item-management.index')->withErrors(['error'=>'Item tidak dapat dinonaktifkan karena masih digunakan dalam Material Request yang belum selesai.']);
        }

        // Toggle status
        $newStatus = $item->is_active ? 0 : 1;
        $item->update(['is_active' => $newStatus]);

        $status = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('item-management.index')->with('success', "Item berhasil $status.");
    }
}
