<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\SubType;
use App\Models\Type;
use Illuminate\Http\Request;

class SubTypeManagementController extends Controller
{
    public function index()
    {
        $subTypes = SubType::with('type')->get(); // Mengambil SubType beserta Type terkait
        return view('pages.subtype-management.index', compact('subTypes'));
    }

    public function create()
    {
        $types = Type::where('is_active', 1)->get();
        return view('pages.subtype-management.form', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_code' => 'required|exists:types,type_code',
            'sub_type_name' => 'required|string|max:255',
            'initial' => 'required|string|max:4|unique:sub_types,initial',
        ]);

        SubType::create([
            'sub_type_code' => SubType::generateSubTypeCode(),
            'sub_type_name' => $validated['sub_type_name'],
            'type_code' => $validated['type_code'],
            'initial' => $validated['initial'],
            'is_active' => 1,
        ]);

        return redirect()->route('subtype-management.index')->with('success', 'Sub Type berhasil ditambahkan.');
    }

    public function edit(SubType $subType)
    {
        $types = Type::where('is_active', 1)->get();
        return view('pages.subtype-management.form', compact('subType', 'types'));
    }

    public function update(Request $request, SubType $subType)
    {
        $validated = $request->validate([
            'type_code' => 'required|exists:types,type_code',
            'sub_type_name' => 'required|string|max:255',
            'initial' => 'required|string|max:4|unique:sub_types,initial,' . $subType->sub_type_code . ',sub_type_code',
        ]);

        $subType->update($validated);

        return redirect()->route('subtype-management.index')->with('success', 'Sub Type berhasil diperbarui.');
    }

    public function toggleStatus(SubType $subType)
    {
        // Cek apakah ada item yang masih aktif menggunakan subtype ini
        $hasActiveItems = $subType->items()->where('is_active', 1)->exists();

        // Jika ada item yang masih aktif, jangan izinkan dinonaktifkan
        if ($subType->is_active === 1 && $hasActiveItems) {
            return redirect()->route('subtype-management.index')
                ->withErrors(['error' => 'Sub Type tidak dapat dinonaktifkan karena masih digunakan oleh Item yang aktif.']);
        }

        // Toggle status SubType
        $newStatus = $subType->is_active === 1 ? 0 : 1;
        $subType->update(['is_active' => $newStatus]);

        $status = $newStatus === 1 ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('subtype-management.index')->with('success', "Sub Type berhasil $status.");
    }

}
