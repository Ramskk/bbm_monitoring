<?php

namespace App\Http\Controllers;

use App\Models\TransaksiBBM;
use App\Models\Kendaraan;
use App\Models\Stok;
use App\Services\ReportService;
use App\Models\PO;
use App\Models\Approval;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $summary = ReportService::dashboardSummary();
        $stokBBM = Stok::all();
        $transaksiTerbaru = TransaksiBBM::latest('tanggal_pemakaian')
            ->whereNull('deleted_at')
            ->limit(5)
            ->get();

        $kendaraanBoros = Kendaraan::whereNull('deleted_at')
            ->where('status', 'aktif')
            ->with('bbm')
            ->whereHas('transaksi', function ($q) {
                $q->whereNotNull('approved_by')
                    ->where('efisiensi', '<', 8);
            })
            ->limit(5)
            ->get();

        $grafikHarian = TransaksiBBM::selectRaw('DATE(tanggal_pemakaian) as day, COUNT(*) as count')
            ->join('bbm', 'transaksi_bbm.bbm_id', '=', 'bbm.id')
            ->whereNull('transaksi_bbm.deleted_at')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $poPending = PO::where('status', 'approved')
            ->where('approved_at', '>=', now()->subDay())
            ->where(function ($q) {
                $q->where('tanggal_kirim_rencana', '<', now())
                  ->orWhere('tanggal_kirim_aktual', '<', now());
            })
            ->with(['vendor', 'bbm'])
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'summary', 'stokBBM', 'transaksiTerbaru', 'kendaraanBoros',
            'grafikHarian', 'poPending'
        ));
    }
}
