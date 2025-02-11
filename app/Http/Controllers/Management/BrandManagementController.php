<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandManagementController extends Controller
{
    public function index()
    {
        $brands = Brand::all();

        return view('pages.brand-management.index', compact('brands'));
    }
    public function create()
    {
        return view('pages.brand-management.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'brand_initial' => 'required|string|max:4|unique:brands,brand_initial',
        ]);

        $brandCode = Brand::generateBrandCode();

        Brand::create([
            'brand_code' => $brandCode,
            'brand_name' => $validated['brand_name'],
            'brand_initial' => $validated['brand_initial'],
            'is_active' => 1,
        ]);

        return redirect()->route('brand-management.index')->with('success', 'Brand berhasil ditambahkan.');
    }

    public function edit(Brand $brand)
    {
        return view('pages.brand-management.form', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'brand_initial' => 'required|string|max:4|unique:brands,brand_initial,' . $brand->brand_code . ',brand_code',
        ]);

        $brand->update($validated);

        return redirect()->route('brand-management.index')->with('success', 'Brand berhasil diperbarui.');
    }

    public function toggleStatus(Brand $brand)
    {
        // Periksa apakah brand terkait dengan produk dan spesifikasi di tabel brand_product_specification
        $hasRelations = \DB::table('brand_product_specification')
            ->where('brand_code', $brand->brand_code)
            ->exists();

        // Jika ada hubungan, batalkan proses nonaktifkan brand
        if ($brand->is_active && $hasRelations) {
            return redirect()->route('brand-management.index')->withErrors([
                'error' => 'Brand tidak dapat dinonaktifkan karena masih memiliki hubungan dengan produk atau spesifikasi.'
            ]);
        }

        // Toggle status brand
        $brand->update(['is_active' => !$brand->is_active]);

        $status = $brand->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('brand-management.index')->with('success', "Brand berhasil $status.");
    }
}
