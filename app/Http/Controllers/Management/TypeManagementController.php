<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeManagementController extends Controller
{
    public function index()
    {
        $types = Type::all();
        return view('pages.type-management.index', compact('types'));
    }

    public function create()
    {
        return view('pages.type-management.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:255',
            'initial' => 'required|string|max:4|unique:types,initial',
        ]);

        Type::create([
            'type_code' => Type::generateTypeCode(),
            'type_name' => $validated['type_name'],
            'initial' => $validated['initial'],
        ]);

        return redirect()->route('type-management.index')->with('success', 'Type berhasil ditambahkan.');
    }

    public function edit(Type $type)
    {
        return view('pages.type-management.form', compact('type'));
    }

    public function update(Request $request, Type $type)
    {
        $validated = $request->validate([
            'type_name' => 'required|string|max:255',
            'initial' => 'required|string|max:4|unique:types,initial,' . $type->type_code . ',type_code',
        ]);

        $type->update($validated);

        return redirect()->route('type-management.index')->with('success', 'Type berhasil diperbarui.');
    }

    public function toggleStatus(Type $type)
    {
        // Periksa apakah ada SubType yang aktif terkait dengan Type
        $hasActiveSubTypes = $type->subTypes()->where('is_active', 1)->exists();

        if ($hasActiveSubTypes) {
            return redirect()->route('type-management.index')->withErrors([
                'error' => 'Type tidak dapat dinonaktifkan karena masih memiliki SubType yang aktif.',
            ]);
        }

        // Toggle status Type
        $newStatus = $type->is_active === 1 ? 0 : 1;
        $type->update(['is_active' => $newStatus]);

        $status = $newStatus === 1 ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('type-management.index')->with('success', "Type berhasil $status.");
    }

}
