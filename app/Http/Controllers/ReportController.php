<?php

namespace App\Http\Controllers;

use App\Services\AuditLogService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function efisiensi(Request $request)
    {
        $departemen = $request->input('departemen');

        $dari = now()->startOfYear();
        $sampai = now();

        $data = ReportService::efisiensiPerKendaraan($dari, $sampai, $departemen ?: null);

        $grafikData = $data->map(function ($item) {
            return [
                'label' => $item->nama.' ('.$item->merek.' '.$item->model.')',
                'efisiensi' => round((float) $item->efisiensi, 2),
                'standar' => round((float) $item->standar, 2),
            ];
        })->values();

        return view('laporan.efisiensi', compact('data', 'grafikData', 'departemen'));
    }

    public function biaya(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));

        $data = ReportService::biayaPerBulan($tahun);

        return view('laporan.biaya', compact('data', 'tahun'));
    }

    public function anomali(Request $request)
    {
        $dari = $request->input('dari', now()->subDays(30)->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $data = ReportService::deteksiAnomali(Carbon::parse($dari), Carbon::parse($sampai), 0.7);

        return view('laporan.anomali', compact('data', 'dari', 'sampai'));
    }

    public function exportEfisiensi(Request $request)
    {
        AuditLogService::log('export', null, null, null, null, 'Export laporan efisiensi');

        return redirect()->route('laporan.efisiensi')
            ->with('success', 'Laporan berhasil diexport.');
    }
}
