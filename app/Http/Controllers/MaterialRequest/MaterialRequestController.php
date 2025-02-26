<?php

namespace App\Http\Controllers\MaterialRequest;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\Project;
use App\Models\Item;
use App\Models\User;
use App\Models\WarehouseLog;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    public function index()
    {
        $materialRequests = MaterialRequest::with('requestor', 'project', 'items')->orderBy('created_at', 'asc')->get();
        return view('pages.material-request.index', compact('materialRequests'));
    }

    public function getItems($mr_code)
    {
        $materialRequest = MaterialRequest::with('items.item')->where('mr_code', $mr_code)->firstOrFail();

        return response()->json([
            'items' => $materialRequest->items->map(function ($item) {
                return [
                    'item_id' => $item->item->item_id ?? '-',
                    'item_code' => $item->item->item_code ?? '-',
                    'description' => $item->item->description ?? '-',
                    'quantity' => $item->quantity,
                    'fulfilled_quantity' => $item->fulfilled_quantity,
                    'status' => ucfirst($item->status),
                ];
            }),
            'material_request_code' => $materialRequest->mr_code,
            'material_request_status' => $materialRequest->status,
        ]);
    }

    public function cancelItem($mr_code, $item_id)
    {
        $materialRequestItem = MaterialRequestItem::where('mr_code', $mr_code)
            ->where('item_id', $item_id)
            ->firstOrFail();

        // Cegah pembatalan jika status item tidak valid
        if (in_array($materialRequestItem->status, ['fulfilled', 'cancelled', 'completed'])) {
            return response()->json(['message' => 'Item tidak dapat dibatalkan karena statusnya tidak valid.'], 400);
        }

        // Update status item menjadi cancelled
        $materialRequestItem->update(['status' => 'cancelled']);

        // Ambil semua item yang terkait dengan MR
        $materialRequestItems = MaterialRequestItem::where('mr_code', $mr_code)->get();

        // Pengecekan kondisi item
        $allCancelled = $materialRequestItems->every(function ($item) {
            return $item->status === 'cancelled';
        });

        $allFulfilledOrCancelled = $materialRequestItems->every(function ($item) {
            return in_array($item->status, ['fulfilled', 'cancelled']);
        });

        $hasPendingOrPartial = $materialRequestItems->contains(function ($item) {
            return in_array($item->status, ['pending', 'partial']);
        });

        // Ambil Material Request
        $materialRequest = MaterialRequest::where('mr_code', $mr_code)->firstOrFail();

        // Cek status MR saat ini, hanya ubah jika status MR bukan "pending" atau "approved"
        if (!in_array($materialRequest->status, ['created', 'approved'])) {
            if ($allCancelled) {
                $materialRequest->update(['status' => 'cancelled']);
            } elseif ($allFulfilledOrCancelled) {
                $materialRequest->update(['status' => 'completed']);
            } elseif ($hasPendingOrPartial) {
                $materialRequest->update(['status' => 'partial']);
            }
        }

        return response()->json(['message' => 'Item berhasil dibatalkan.']);
    }

    public function create()
    {
        $lastMr = MaterialRequest::latest('mr_code')->first();
        $lastMrNumber = $lastMr ? intval(substr($lastMr->mr_code, 3)) : 0;

        $projects = Project::all();
        $users = User::where('is_active', 1)->get();
        $availableItems = Item::where('is_active', 1)->get();

        return view('pages.material-request.form', compact('lastMrNumber', 'projects', 'users', 'availableItems'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,project_id',
            'note' => 'required|string|max:255',
            'created_by' => 'required|exists:users,user_id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,item_id',
            'items.*.quantity' => 'required|numeric|min:1',
        ], [
            // Pesan kesalahan khusus
            'project_id.required' => 'Proyek harus dipilih.',
            'note.required' => 'Catatan tidak boleh kosong.',
            'created_by.required' => 'Requestor harus dipilih.',
            'items.required' => 'Minimal 1 item harus ditambahkan.',
            'items.*.item_id.required' => 'Item harus dipilih.',
            'items.*.quantity.required' => 'Kuantitas item tidak boleh kosong.',
            'items.*.quantity.min' => 'Kuantitas item harus lebih dari 0.',
        ]);

        $mrCode = 'MR-' . str_pad(MaterialRequest::count() + 1, 6, '0', STR_PAD_LEFT);

        $materialRequest = MaterialRequest::create([
            'mr_code' => $mrCode,
            'project_id' => $validated['project_id'],
            'note' => $validated['note'],
            'created_by' => $validated['created_by'],
            'status' => 'created',
        ]);

        foreach ($validated['items'] as $item) {
            MaterialRequestItem::create([
                'mr_code' => $mrCode,
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'fulfilled_quantity' => 0,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('material-request.index')->with('success', "{$mrCode} berhasil dibuat.");
    }

    public function edit($mr_code)
    {
        $materialRequest = MaterialRequest::with('items')->where('mr_code', $mr_code)->firstOrFail();
        $lastMr = MaterialRequest::latest('mr_code')->first();
        $lastMrNumber = $lastMr ? intval(substr($lastMr->mr_code, 3)) : 0;

        $projects = Project::all();
        $users = User::where('is_active', 1)->get();
        $availableItems = Item::where('is_active', 1)->get();

        return view('pages.material-request.form', compact('materialRequest', 'lastMrNumber', 'projects', 'users', 'availableItems'));
    }

    public function update(Request $request, $mrCode)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,item_id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        MaterialRequestItem::where('mr_code', $mrCode)->delete();

        foreach ($validated['items'] as $item) {
            MaterialRequestItem::create([
                'mr_code' => $mrCode,
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'fulfilled_quantity' => 0,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('material-request.index')->with('success', "{$mrCode} berhasil diperbarui.");
    }

    public function approve($mrCode)
    {
        $materialRequest = MaterialRequest::where('mr_code', $mrCode)->first();

        if (!$materialRequest) {
            return response()->json(['message' => 'Material Request tidak ditemukan'], 404);
        }

        if ($materialRequest->status !== 'created') {
            return response()->json(['message' => 'Material Request hanya dapat disetujui jika dalam status "created"'], 400);
        }

        // Perbarui status ke "approved"
        $materialRequest->status = 'approved';
        $materialRequest->save();

        return response()->json(['message' => 'Material Request berhasil disetujui', 'status' => $materialRequest->status]);
    }

    public function release(Request $request, $mrCode, $itemId)
    {
        $validated = $request->validate([
            'fulfilled_quantity' => 'required|integer|min:1',
        ]);

        $item = MaterialRequestItem::where('mr_code', $mrCode)->where('item_id', $itemId)->first();

        if (!$item) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        $remainingQuantity = $item->quantity - $item->fulfilled_quantity;
        if ($validated['fulfilled_quantity'] > $remainingQuantity) {
            return response()->json(['message' => 'Jumlah melebihi sisa permintaan'], 400);
        }

        // Update fulfilled_quantity
        $item->fulfilled_quantity += $validated['fulfilled_quantity'];
        $item->status = $item->fulfilled_quantity < $item->quantity ? 'partial' : 'fulfilled';
        $item->save();

        // Simpan log ke WarehouseLog
        WarehouseLog::create([
            'mr_item_id' => $item->id,
            'fulfilled_quantity' => $validated['fulfilled_quantity'],
            'remaining_quantity' => $item->quantity - $item->fulfilled_quantity,
        ]);

        // Update status Material Request
        $materialRequest = MaterialRequest::where('mr_code', $mrCode)->first();
        $allItems = $materialRequest->items;

        $allFulfilledOrCancelled = $allItems->every(fn($i) => in_array($i->status, ['fulfilled', 'cancelled']));
        $hasPartialOrFulfilled = $allItems->contains(fn($i) => in_array($i->status, ['partial', 'fulfilled']));

        if ($allFulfilledOrCancelled) {
            $materialRequest->status = 'completed';
        } elseif ($hasPartialOrFulfilled) {
            $materialRequest->status = 'partial';
        }

        $materialRequest->save();

        // Return response with updated MR status
        return response()->json([
            'message' => 'Item berhasil di-release',
            'material_request_status' => $materialRequest->status,
        ]);
    }

    public function cancel($materialRequestId)
    {
        $materialRequest = MaterialRequest::with('items')->where('mr_code', $materialRequestId)->firstOrFail();

        // Update status MR
        $materialRequest->status = 'cancelled';
        $materialRequest->save();

        // Cancel semua item yang belum fulfilled
        foreach ($materialRequest->items as $item) {
            if ($item->status !== 'fulfilled') {
                $item->status = 'cancelled';
                $item->save();
            }
        }

        return response()->json(['message' => "Material Request {$materialRequest->mr_code} berhasil dibatalkan."]);
    }
}
