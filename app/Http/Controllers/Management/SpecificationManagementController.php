<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Specification;
use Illuminate\Http\Request;

class SpecificationManagementController extends Controller
{
    public function index()
    {
        $specifications = Specification::all();
        return view('pages.specification-management.index', compact('specifications'));
    }

    public function create()
    {
        return view('pages.specification-management.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'specification_name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
        ]);

        Specification::create([
            'specification_name' => $validated['specification_name'],
            'unit' => $validated['unit'],
            'is_active' => 1,
        ]);

        return redirect()->route('specification-management.index')->with('success', 'Spesifikasi berhasil ditambahkan.');
    }

    public function edit(Specification $specification)
    {
        return view('pages.specification-management.form', compact('specification'));
    }

    public function update(Request $request, Specification $specification)
    {
        $validated = $request->validate([
            'specification_name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
        ]);

        $specification->update($validated);

        return redirect()->route('specification-management.index')->with('success', 'Spesifikasi berhasil diperbarui.');
    }

    public function toggleStatus(Specification $specification)
    {
        // Periksa apakah spesifikasi memiliki hubungan dengan brand dan produk di tabel brand_product_specification
        $hasRelations = \DB::table('brand_product_specification')
            ->where('specification_id', $specification->specification_id)
            ->exists();

        // Jika spesifikasi memiliki hubungan, tampilkan pesan error
        if ($specification->is_active && $hasRelations) {
            return redirect()->route('specification-management.index')->withErrors([
                'error' => 'Spesifikasi tidak dapat dinonaktifkan karena masih terkait dengan produk atau brand.'
            ]);
        }

        // Toggle status spesifikasi
        $specification->update(['is_active' => !$specification->is_active]);

        $status = $specification->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('specification-management.index')->with(
            'success',
            "Spesifikasi berhasil $status."
        );
    }
}
