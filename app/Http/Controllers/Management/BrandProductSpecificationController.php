<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Specification;
use Illuminate\Http\Request;

class BrandProductSpecificationController extends Controller
{
    public function index()
    {
        $relations = \DB::table('brand_product_specification')
            ->join('brands', 'brand_product_specification.brand_code', '=', 'brands.brand_code')
            ->join('products', 'brand_product_specification.product_code', '=', 'products.product_code')
            ->join('specifications', 'brand_product_specification.specification_id', '=', 'specifications.specification_id')
            ->select(
                'brand_product_specification.brand_code',
                'brand_product_specification.product_code',
                \DB::raw('MIN(brand_product_specification.id) as id'),
                'brands.brand_name',
                'products.product_name',
                \DB::raw('GROUP_CONCAT(specifications.specification_name SEPARATOR ", ") as specifications'),
                \DB::raw('MAX(brand_product_specification.is_active) as is_active')
            )
            ->where('brand_product_specification.is_active', 1) // Filter hanya data aktif
            ->groupBy(
                'brand_product_specification.brand_code',
                'brand_product_specification.product_code',
                'brands.brand_name',
                'products.product_name'
            )
            ->get();

        return view('pages.brand-product-specification.index', compact('relations'));
    }

    public function create()
    {
        $brands = Brand::where('is_active', 1)->get();
        $products = Product::where('is_active', 1)->get();
        $specifications = Specification::where('is_active', 1)->get();

        return view('pages.brand-product-specification.form', compact('brands', 'products', 'specifications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_code' => 'required|exists:brands,brand_code',
            'product_code' => 'required|exists:products,product_code',
            'specifications' => 'required|array',
            'specifications.*' => 'exists:specifications,specification_id',
        ]);

        foreach ($validated['specifications'] as $specificationId) {
            // Cek apakah kombinasi brand_code, product_code, dan specification_id sudah ada
            $existingRelation = \DB::table('brand_product_specification')
                ->where('brand_code', $validated['brand_code'])
                ->where('product_code', $validated['product_code'])
                ->where('specification_id', $specificationId)
                ->first();

            if ($existingRelation) {
                // Jika pasangan sudah ada dan nonaktif, aktifkan kembali
                if (!$existingRelation->is_active) {
                    \DB::table('brand_product_specification')
                        ->where('id', $existingRelation->id)
                        ->update([
                            'is_active' => 1,
                            'updated_at' => now(),
                        ]);
                }
                // Jika pasangan sudah ada dan aktif, tidak perlu melakukan apa-apa
                continue;
            }

            // Jika pasangan belum ada, tambahkan data baru
            \DB::table('brand_product_specification')->insert([
                'brand_code' => $validated['brand_code'],
                'product_code' => $validated['product_code'],
                'specification_id' => $specificationId,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('brand-product-specification.index')->with('success', 'Hubungan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $relation = \DB::table('brand_product_specification')
            ->where('id', $id)
            ->first();

        if (!$relation) {
            return redirect()->route('brand-product-specification.index')
                ->withErrors(['error' => 'Data tidak ditemukan.']);
        }

        $brands = Brand::where('is_active', 1)->get();
        $products = Product::where('is_active', 1)->get();
        $specifications = Specification::where('is_active', 1)->get();

        $selectedSpecifications = \DB::table('brand_product_specification')
            ->where('brand_code', $relation->brand_code)
            ->where('product_code', $relation->product_code)
            ->where('is_active', 1) // Hanya ambil spesifikasi yang aktif
            ->pluck('specification_id')
            ->toArray();

        return view('pages.brand-product-specification.form', compact(
            'relation',
            'brands',
            'products',
            'specifications',
            'selectedSpecifications'
        ));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'brand_code' => 'required|exists:brands,brand_code',
            'product_code' => 'required|exists:products,product_code',
            'specifications' => 'required|array',
            'specifications.*' => 'exists:specifications,specification_id',
        ]);

        $existingSpecifications = \DB::table('brand_product_specification')
            ->where('brand_code', $validated['brand_code'])
            ->where('product_code', $validated['product_code'])
            ->get();

        $existingSpecificationIds = $existingSpecifications->pluck('specification_id')->toArray();
        $activeSpecifications = $existingSpecifications->where('is_active', 1)->pluck('specification_id')->toArray();

        $specificationsToAdd = array_diff($validated['specifications'], $existingSpecificationIds);
        $specificationsToActivate = array_intersect($validated['specifications'], array_diff($existingSpecificationIds, $activeSpecifications));
        $specificationsToDeactivate = array_diff($activeSpecifications, $validated['specifications']);

        // Tambahkan spesifikasi baru
        foreach ($specificationsToAdd as $specificationId) {
            \DB::table('brand_product_specification')->insert([
                'brand_code' => $validated['brand_code'],
                'product_code' => $validated['product_code'],
                'specification_id' => $specificationId,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Aktifkan spesifikasi yang sebelumnya nonaktif
        if (!empty($specificationsToActivate)) {
            \DB::table('brand_product_specification')
                ->where('brand_code', $validated['brand_code'])
                ->where('product_code', $validated['product_code'])
                ->whereIn('specification_id', $specificationsToActivate)
                ->update([
                    'is_active' => 1,
                    'updated_at' => now(),
                ]);
        }

        // Nonaktifkan spesifikasi yang dihapus dari daftar
        if (!empty($specificationsToDeactivate)) {
            \DB::table('brand_product_specification')
                ->where('brand_code', $validated['brand_code'])
                ->where('product_code', $validated['product_code'])
                ->whereIn('specification_id', $specificationsToDeactivate)
                ->update([
                    'is_active' => 0,
                    'updated_at' => now(),
                ]);
        }

        return redirect()->route('brand-product-specification.index')->with('success', 'Hubungan berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        // Ambil relasi berdasarkan ID
        $relation = \DB::table('brand_product_specification')
            ->join('brands', 'brand_product_specification.brand_code', '=', 'brands.brand_code')
            ->join('products', 'brand_product_specification.product_code', '=', 'products.product_code')
            ->select(
                'brand_product_specification.*',
                'brands.brand_name as brand_name',
                'products.product_name as product_name'
            )
            ->where('brand_product_specification.id', $id)
            ->first();


        if (!$relation) {
            return redirect()->route('brand-product-specification.index')->withErrors(['error' => 'Data tidak ditemukan.']);
        }

        // Periksa apakah pasangan brand_code dan product_code digunakan di tabel items
        $isUsedInItem = \DB::table('items')
            ->where('brand_code', $relation->brand_code)
            ->where('product_code', $relation->product_code)
            ->exists();

        if ($isUsedInItem) {
            return redirect()->route('brand-product-specification.index')
                ->withErrors(['error' => 'Tidak dapat mengubah status karena pasangan ini sedang digunakan pada item.']);
        }

        // Ambil status saat ini (aktif atau nonaktif)
        $currentStatus = $relation->is_active;

        // Toggle status untuk semua spesifikasi terkait brand_code dan product_code
        \DB::table('brand_product_specification')
            ->where('brand_code', $relation->brand_code)
            ->where('product_code', $relation->product_code)
            ->update([
                'is_active' => !$currentStatus,
                'updated_at' => now(),
            ]);

        $statusText = !$currentStatus ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('brand-product-specification.index')->with('success', "Semua spesifikasi untuk pasangan {$relation->brand_name} - {$relation->product_name} berhasil $statusText.");
    }
}
