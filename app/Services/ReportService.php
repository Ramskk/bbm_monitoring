<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\Stok;
use App\Models\TransaksiBBM;
use App\Models\Kendaraan;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $reportService = app(ReportService::class);

        $summary = $reportService->dashboardSummary();

        $stokBBM = Stok::all();

        $transaksiTerbaru = TransaksiBBM::latest('tanggal_pemakaian')
            ->whereNull('deleted_at')
            ->limit(5)
            ->get();

        $kendaraanBoros = Kendaraan::whereNull('deleted_at')
            ->where('status', 'aktif')
            ->with('bbm')
            ->whereHas('transaksi', function ($q) {
                $q->whereNotNull('approved_by');
            })
            ->limit(5)
            ->get();

        return view('dashboard', [
            'summary' => $summary,
            'stokBBM' => $stokBBM,
            'transaksiTerbaru' => $transaksiTerbaru,
            'kendaraanBoros' => $kendaraanBoros,
        ]);
    }
}