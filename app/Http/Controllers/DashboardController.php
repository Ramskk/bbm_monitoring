<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\TransaksiBBM;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $summary = ReportService::dashboardSummary();

        $recentTransactions = TransaksiBBM::with(['kendaraan', 'bbm'])
            ->latest('tanggal_pemakaian')
            ->limit(5)
            ->get();

        $stokList = Stok::with('bbm')->get();

        $stokChartData = [
            'labels' => $stokList->map(fn ($stok) => $stok->bbm->nama ?? 'BBM #'.$stok->bbm_id)->all(),
            'datasets' => [[
                'data' => $stokList->map(fn ($stok) => $stok->jumlah)->all(),
                'backgroundColor' => ['#0066cc', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#20c997'],
            ]],
        ];

        $efisiensiData = TransaksiBBM::selectRaw('DATE(tanggal_pemakaian) as hari, AVG(efisiensi) as rata_efisiensi')
            ->whereNotNull('efisiensi')
            ->where('tanggal_pemakaian', '>=', now()->subDays(7))
            ->groupBy('hari')
            ->orderBy('hari')
            ->get();

        $efisiensiChartData = [
            'labels' => $efisiensiData->pluck('hari')->all(),
            'datasets' => [[
                'label' => 'Efisiensi (km/L)',
                'data' => $efisiensiData->map(fn ($row) => round((float) $row->rata_efisiensi, 2))->all(),
                'borderColor' => '#0066cc',
                'fill' => false,
            ]],
        ];

        return view('dashboard', array_merge($summary, [
            'recentTransactions' => $recentTransactions,
            'stokChartData' => $stokChartData,
            'efisiensiChartData' => $efisiensiChartData,
        ]));
    }
}
