<?php

namespace App\Http\Controllers;

use App\Models\MaterialRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua tahun dan bulan dari data MaterialRequest
        $availableMonths = MaterialRequest::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month')
            ->distinct()
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Ambil bulan dan tahun dari request atau default ke bulan dan tahun terbaru
        $latestRequest = MaterialRequest::latest('created_at')->first();
        $month = $request->input('month', $latestRequest ? $latestRequest->created_at->format('m') : now()->format('m'));
        $year = $request->input('year', $latestRequest ? $latestRequest->created_at->format('Y') : now()->format('Y'));

        // Pastikan bulan dan tahun valid
        if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
            abort(400, 'Invalid year or month');
        }

        // Query data berdasarkan bulan dan tahun
        $statusCounts = MaterialRequest::select('status', \DB::raw('count(*) as count'))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('status')
            ->get();

        // Pastikan statusCounts tidak null
        if ($statusCounts->isEmpty()) {
            $statusCounts = collect(); // Return empty collection if no data
        }

        return view('pages.dashboard', compact('statusCounts', 'availableMonths', 'month', 'year'));
    }

    public function getAvailableMonths($year)
    {
        // Validasi input tahun
        if (!is_numeric($year) || strlen($year) !== 4) {
            return response()->json(['error' => 'Invalid year'], 400);
        }

        $months = MaterialRequest::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month')
            ->distinct()
            ->orderBy(('month'))
            ->pluck('month');

        return response()->json($months);
    }

    public function getChartData($year, $month)
    {
        // Validasi input
        if (!is_numeric($year) || strlen($year) !== 4 || !is_numeric($month) || $month < 1 || $month > 12) {
            return response()->json(['error' => 'Invalid year or month'], 400);
        }

        // Default statuses and colors
        $statuses = ['pending', 'partial', 'fulfilled', 'cancelled'];
        $colors = [
            'pending' => '#007bff',
            'partial' => '#ffc107',
            'fulfilled' => '#dc3545',
            'cancelled' => '#6c757d',
        ];

        // Query untuk mendapatkan data
        $queryResults = \DB::table('material_request_items')
            ->join('material_requests', 'material_request_items.mr_code', '=', 'material_requests.mr_code')
            ->whereYear('material_requests.created_at', $year)
            ->whereMonth('material_requests.created_at', $month)
            ->selectRaw('material_request_items.status, COUNT(material_request_items.mr_code) as count')
            ->groupBy('material_request_items.status')
            ->pluck('count', 'status'); // Format hasil: ['pending' => 10, 'fulfilled' => 5, ...]

        // Gabungkan hasil query dengan default
        $data = [];
        foreach ($statuses as $status) {
            $data[] = $queryResults[$status] ?? 0; // Jika status tidak ditemukan, gunakan 0
        }

        // Response JSON
        return response()->json([
            'labels' => $statuses, // Labels mengikuti urutan default
            'data' => $data,       // Data sesuai urutan status
            'colors' => array_values($colors), // Warna sesuai urutan default
        ]);
    }


    public function getMonthlyStatusChart($year)
    {
        $statuses = ['created', 'approved', 'partial', 'completed', 'cancelled'];
        $colors = [
            'created' => '#007bff',
            'approved' => '#28a745',
            'partial' => '#ffc107',
            'completed' => '#dc3545',
            'cancelled' => '#6c757d',
        ];

        // Data awal untuk semua bulan (1-12)
        $data = array_fill(0, 12, array_fill_keys($statuses, 0)); // Mulai dari indeks 0 untuk bulan Januari

        // Ambil data dari database
        $monthlyData = MaterialRequest::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, LOWER(status) as status, COUNT(*) as count')
            ->groupBy('month', 'status')
            ->orderBy('month')
            ->get();

        // Susun data berdasarkan bulan dan status
        foreach ($monthlyData as $entry) {
            if (array_key_exists($entry->status, $colors)) {
                $data[$entry->month - 1][$entry->status] = $entry->count; // Index bulan dimulai dari 0
            }
        }

        // Buat dataset untuk Chart.js
        $datasets = [];
        foreach ($statuses as $status) {
            $datasets[] = [
                'status' => $status, // Formatkan untuk frontend
                'data' => array_column($data, $status),
                'color' => $colors[$status],
            ];
        }

        return response()->json([
            'labels' => ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            'datasets' => $datasets,
        ]);
    }

    public function getTopItems($year, $month)
    {
        $items = \DB::table('material_request_items')
            ->join('items', 'material_request_items.item_id', '=', 'items.item_id')
            ->join('material_requests', 'material_request_items.mr_code', '=', 'material_requests.mr_code')
            ->whereYear('material_requests.created_at', $year)
            ->whereMonth('material_requests.created_at', $month)
            ->where('material_request_items.status', '!=', 'cancelled') // Hanya hitung yang bukan "cancelled"
            ->select('items.description', \DB::raw('SUM(material_request_items.quantity) as total_quantity')) // Menggunakan SUM untuk quantity
            ->groupBy('items.description')
            ->orderBy('total_quantity', 'desc') // Urutkan berdasarkan total_quantity
            ->limit(5)
            ->get();

        return response()->json($items);
    }

    public function getTopProjects($year, $month)
    {
        $projects = \DB::table('material_requests')
            ->join('projects', 'material_requests.project_id', '=', 'projects.project_id')
            ->join('material_request_items', 'material_requests.mr_code', '=', 'material_request_items.mr_code')
            ->whereYear('material_requests.created_at', $year)
            ->whereMonth('material_requests.created_at', $month)
            ->where('material_request_items.status', '!=', 'cancelled') // Hanya hitung yang tidak "cancelled"
            ->select('projects.project_name', \DB::raw('SUM(material_request_items.quantity) as total_quantity')) // Hitung total quantity
            ->groupBy('projects.project_name')
            ->orderBy('total_quantity', 'desc') // Urutkan berdasarkan total_quantity
            ->limit(5)
            ->get();

        return response()->json($projects);
    }
}
