<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Kendaraan;
use App\Models\TransaksiBBM;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Ringkasan angka untuk widget dashboard.
     *
     * @return array<string, mixed>
     */
    public static function dashboardSummary(): array
    {
        $awalBulan = now()->startOfMonth();

        $transaksiBulanIni = TransaksiBBM::where('tanggal_pemakaian', '>=', $awalBulan);

        return [
            'totalKendaraan' => Kendaraan::count(),
            'totalKendaraanAktif' => Kendaraan::where('status', 'Aktif')->count(),
            'transaksiMonth' => (clone $transaksiBulanIni)->count(),
            'totalLitersMonth' => round((float) (clone $transaksiBulanIni)->sum('jumlah_liter'), 2),
            'stokKritis' => StockService::getStokKritis(),
            'pendingApprovals' => Approval::where('status', 'pending')->count(),
        ];
    }

    /**
     * Efisiensi rata-rata per kendaraan beserta deviasinya terhadap standar.
     */
    public static function efisiensiPerKendaraan(Carbon $dari, Carbon $sampai, ?string $departemen = null): Collection
    {
        $query = Kendaraan::with('bbm');

        if ($departemen) {
            $query->where('departemen', $departemen);
        }

        return $query->get()->map(function (Kendaraan $kendaraan) use ($dari, $sampai) {
            $rataEfisiensi = (float) $kendaraan->transaksi()
                ->where('status', 'approved')
                ->whereNotNull('efisiensi')
                ->whereBetween('tanggal_pemakaian', [$dari, $sampai])
                ->avg('efisiensi');

            $standar = (float) ($kendaraan->konsumsi_bbm_standar ?? 0);

            $kendaraan->setAttribute('efisiensi', round($rataEfisiensi, 2));
            $kendaraan->setAttribute('standar', round($standar, 2));
            $kendaraan->setAttribute(
                'deviasi_efisiensi',
                $standar > 0 ? round((($rataEfisiensi - $standar) / $standar) * 100, 2) : 0.0
            );
            $kendaraan->setAttribute('status_efisiensi', self::statusEfisiensi($rataEfisiensi, $standar));

            return $kendaraan;
        });
    }

    /**
     * Total biaya BBM per bulan untuk satu tahun.
     */
    public static function biayaPerBulan(int $tahun): Collection
    {
        $rows = TransaksiBBM::whereYear('tanggal_pemakaian', $tahun)
            ->where('status', 'approved')
            ->get(['tanggal_pemakaian', 'total_biaya'])
            ->groupBy(fn (TransaksiBBM $trx) => (int) $trx->tanggal_pemakaian->format('n'))
            ->map(fn (Collection $items) => $items->sum('total_biaya'));

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return collect(range(1, 12))->map(function (int $bulan) use ($rows, $namaBulan) {
            return (object) [
                'bulan' => $namaBulan[$bulan],
                'biaya' => round((float) ($rows[$bulan] ?? 0), 2),
            ];
        });
    }

    /**
     * Deteksi kendaraan dengan efisiensi di bawah ambang batas standar.
     */
    public static function deteksiAnomali(Carbon $dari, Carbon $sampai, float $threshold = 0.7): Collection
    {
        return self::efisiensiPerKendaraan($dari, $sampai)
            ->filter(function (Kendaraan $kendaraan) use ($threshold) {
                $standar = (float) $kendaraan->standar;
                $efisiensi = (float) $kendaraan->efisiensi;

                return $standar > 0
                    && $efisiensi > 0
                    && $efisiensi < ($standar * $threshold);
            })
            ->values();
    }

    /**
     * Tentukan status efisiensi terhadap standar.
     */
    protected static function statusEfisiensi(float $efisiensi, float $standar): string
    {
        if ($standar <= 0 || $efisiensi <= 0) {
            return 'tidak_ada_data';
        }

        if ($efisiensi >= $standar * 1.2) {
            return 'sangat_baik';
        }

        if ($efisiensi >= $standar * 0.8) {
            return 'normal';
        }

        return 'boros';
    }
}
