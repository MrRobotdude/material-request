<?php

namespace App\Http\Controllers\MaterialRequest;

use App\Http\Controllers\Controller;
use App\Models\WarehouseLog;
use Illuminate\Http\Request;

class WarehouseLogController extends Controller
{
    public function index()
    {
        // Ambil semua log dengan relasi ke MaterialRequestItem, MaterialRequest, dan Item
        $logs = WarehouseLog::with([
            'materialRequestItem.materialRequest', // Relasi ke MaterialRequest
            'materialRequestItem.item',           // Relasi ke Item
        ])->get();

        return view('pages.warehouse-log.index', compact('logs'));
    }
}
