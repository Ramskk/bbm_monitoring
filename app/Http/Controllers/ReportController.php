<?php

namespace App\Http\Controllers;

use App\Models\TransaksiBBM;
use App\Models\Kendaraan;
use App\Models\BBM;
use App\Models\PO;
use App\Services\ReportService;
use App\Models\Approval;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function efisiensi(Request $request)
    {
        $query = TransaksiBBM::whereNull('deleted_at')
            ->join('kendaraans', 'transaksi_bbm.kendaraan_id', '=', 'kendaraans.id')
            ->join('bbms', 'kendaraans.bbmid', '=', 'bbms.id')
            ->leftJoin('vendors', 'vendors.id', 'kendaraans.vendor_id')
            ->groupBy('kendaraans.id', 'kendaraans.nama', 'kendaraans.merek', 'kendaraans.model',
                       'kendaraans.tahun', 'kendaraans.jenis', 'bbms.jenis', 'kendaraans.departemen',
                       'kendaraans.pengemudi_default', 'vendors.nama as vendor',
                       'vendors.kota as vendor_kota');

        // Filter by departemen
        $departemen = $request->input('departemen');
        if ($departemen) {
            $query->where('kendaraans.departemen', $departemen);
        }

        $data = $query->orderBy('kendaraans.id', 'asc')
            ->get();

        // Format data untuk grafik
        $grafikData = $data->map(function ($item) {
            return [
                'label'    => $item->nama . ' (' . $item->merek . ' ' . $item->model . ')',
                'efisiensi' => round($item->efisiensi, 2),
                'standar'   => round($item->bbm->konsumsi_bbm_standar, 2),
            ];
        });

        return view('laporan.efisiensi', compact('data', 'grafikData'));
    }

    public function biaya(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $data = ReportService::biayaPerBulan($tahun);

        $grafikData = $data->map(function ($item) {
            return [
                'label'    => $item->bulan,
                'biaya'    => number_format($item->biaya, 2),
            ];
        });

        return view('laporan.biaya', compact('data', 'grafikData'));
    }

    public function anomali(Request $request)
    {
        $dari = $request->input('dari', now()->subDays(30));
        $sampai = $request->input('sampai', now());

        $data = ReportService::deteksiAnomali($dari, $sampai, 0.7);

        $tableData = $data->map(function ($item) {
            return [
                'nomor_polisi'   => $item->nomor_polisi,
                'nama'           => $item->nama,
                'merek_model'    => $item->merek . ' ' . $item->model,
                'efisiensi'      => round($item->efisiensi, 2),
                'standar'        => round($item->bbm->konsumsi_bbm_standar, 2),
                'deviasi'        => round($item->deviasi_efisiensi, 2),
                'status'         => $item->status_efisiensi,
            ];
        });

        return view('laporan.anomali', compact('data', 'tableData'));
    }

    public function exportEfisiensi(Request $request)
    {
        // Audit log
        AuditLogService::log('export', $request->input('jenis', 'laporan'), null, null, null, 'Export laporan efisiensi');

        // Generate Excel/PDF (implementasi Maatwebsite Excel / DomPDF)
        // ...
        // return response()->download(...);

        return redirect()->route('laporan.efisiensi')
            ->with('success', 'Laporan berhasil diexport.');
    }
}
