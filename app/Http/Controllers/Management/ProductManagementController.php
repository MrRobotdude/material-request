<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Specification;
use Illuminate\Http\Request;

class ProductManagementController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('pages.product-management.index', compact('products'));
    }
    
    public function create()
    {
        return view('pages.product-management.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_initial' => 'required|string|max:10|unique:products,product_initial',
        ]);

        Product::create([
            'product_code' => Product::generateProductCode(),
            'product_name' => $validated['product_name'],
            'product_initial' => $validated['product_initial'],
            'is_active' => 1,
        ]);

        return redirect()->route('product-management.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('pages.product-management.form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_initial' => 'required|string|max:10|unique:products,product_initial,' . $product->product_code . ',product_code',
        ]);

        $product->update($validated);

        return redirect()->route('product-management.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function toggleStatus(Product $product)
    {
        // Periksa apakah produk memiliki hubungan dengan brand atau spesifikasi di tabel brand_product_specification
        $hasRelations = \DB::table('brand_product_specification')
            ->where('product_code', $product->product_code)
            ->exists();

        // Jika produk memiliki hubungan, batalkan proses nonaktifkan produk
        if ($product->is_active && $hasRelations) {
            return redirect()->route('product-management.index')->withErrors([
                'error' => 'Produk tidak dapat dinonaktifkan karena masih memiliki hubungan dengan brand atau spesifikasi.'
            ]);
        }

        // Toggle status produk
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('product-management.index')->with(
            'success',
            "Produk berhasil $status."
        );
    }
}