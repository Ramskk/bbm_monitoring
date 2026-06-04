<?php

namespace App\Services;

use App\Models\BBM;
use App\Models\MutasiStok;
use App\Models\Stok;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Kurangi stok berdasarkan jumlah liter.
     */
    public static function kurangiStok(int $bbmId, float $jumlahLiter, string $referensiNo, string $referensiType, int $referensiId, string $keterangan): array
    {
        $result = [];

        try {
            $bbm = BBM::with('stok')->find($bbmId);
            if (! $bbm) {
                throw new \RuntimeException("BBM dengan id {$bbmId} tidak ditemukan.");
            }

            $stok = $bbm->stok;
            if (! $stok) {
                throw new \RuntimeException("Stok untuk BBM {$bbm->nama} tidak ditemukan.");
            }

            // Lock stok untuk mencegah race condition
            $stok = Stok::lockForUpdate()->where('bbm_id', $bbmId)->first();
            if (! $stok) {
                throw new \RuntimeException("Stok untuk BBM {$bbm->nama} tidak ditemukan.");
            }

            $stokSebelum = $stok->jumlah;
            $stokSesudah = $stok->jumlah - $jumlahLiter;

            if ($stokSesudah < 0) {
                throw new \RuntimeException(
                    "Stok {$bbm->nama} tidak cukup. Stok saat ini: {$stokSebelum} liter, yang diminta: {$jumlahLiter} liter."
                );
            }

            // Update stok
            $stok->jumlah = $stokSesudah;
            $stok->save();

            // Buat record MutasiStok
            $mutasiStok = MutasiStok::create([
                'stok_id' => $stok->id,
                'bbm_id' => $bbmId,
                'jenis' => 'keluar',
                'jumlah' => $jumlahLiter,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'harga_per_liter' => $bbm->harga_per_liter,
                'referensi_no' => $referensiNo,
                'referensi_type' => $referensiType,
                'referensi_id' => $referensiId,
                'keterangan' => $keterangan,
                'tanggal' => now(),
                'user_id' => auth()->id(),
            ]);

            $result = [
                'mutasi_stok' => $mutasiStok,
            ];

        } catch (\Throwable $e) {
            Log::error('StockService::kurangiStok error: '.$e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * Tambah stok berdasarkan jumlah liter.
     */
    public static function tambahStok(int $bbmId, float $jumlahLiter, float $hargaPerLiter, string $referensiNo, string $referensiType, int $referensiId): array
    {
        $result = [];

        try {
            $bbm = BBM::with('stok')->find($bbmId);
            if (! $bbm) {
                throw new \RuntimeException("BBM dengan id {$bbmId} tidak ditemukan.");
            }

            $stok = $bbm->stok;
            if (! $stok) {
                throw new \RuntimeException("Stok untuk BBM {$bbm->nama} tidak ditemukan.");
            }

            // Lock stok untuk mencegah race condition
            $stok = Stok::lockForUpdate()->where('bbm_id', $bbmId)->first();
            if (! $stok) {
                throw new \RuntimeException("Stok untuk BBM {$bbm->nama} tidak ditemukan.");
            }

            // Cek kapasitas maksimum berdasarkan data yang sudah dikunci
            if ($stok->jumlah + $jumlahLiter > $stok->stok_maksimum) {
                throw new \RuntimeException(
                    "Stok {$bbm->nama} tidak dapat ditambahkan. Stok saat ini: {$stok->jumlah} liter, kapasitas maksimum: {$stok->stok_maksimum} liter, yang ingin ditambahkan: {$jumlahLiter} liter."
                );
            }

            $stokSebelum = $stok->jumlah;
            $stokSesudah = $stok->jumlah + $jumlahLiter;

            // Update stok
            $stok->jumlah = $stokSesudah;
            $stok->save();

            // Update harga per liter BBM jika berubah
            if (abs($hargaPerLiter - $bbm->harga_per_liter) > 0) {
                $bbm->harga_per_liter = $hargaPerLiter;
                $bbm->save();
            }

            // Buat record MutasiStok
            $mutasiStok = MutasiStok::create([
                'stok_id' => $stok->id,
                'bbm_id' => $bbmId,
                'jenis' => 'masuk',
                'jumlah' => $jumlahLiter,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'harga_per_liter' => $hargaPerLiter,
                'referensi_no' => $referensiNo,
                'referensi_type' => $referensiType,
                'referensi_id' => $referensiId,
                'keterangan' => 'Stok masuk',
                'tanggal' => now(),
                'user_id' => auth()->id(),
            ]);

            $result = [
                'mutasi_stok' => $mutasiStok,
            ];

        } catch (\Throwable $e) {
            Log::error('StockService::tambahStok error: '.$e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * Rollback mutasi stok yang keluar.
     */
    public static function rollbackKeluar(int $mutasiStokId): array
    {
        $result = [];

        try {
            $mutasiStok = MutasiStok::find($mutasiStokId);
            if (! $mutasiStok) {
                throw new \RuntimeException("Mutasi stok dengan id {$mutasiStokId} tidak ditemukan.");
            }

            $stok = $mutasiStok->stok;
            if (! $stok) {
                throw new \RuntimeException("Stok untuk mutasi stok {$mutasiStokId} tidak ditemukan.");
            }

            // Lock stok untuk mencegah race condition
            $stok = Stok::lockForUpdate()->where('bbm_id', $mutasiStok->bbm_id)->first();
            if (! $stok) {
                throw new \RuntimeException("Stok untuk mutasi stok {$mutasiStokId} tidak ditemukan.");
            }

            $stokSebelum = $stok->jumlah;
            $stokSesudah = $stok->jumlah + $mutasiStok->jumlah;

            // Update stok
            $stok->jumlah = $stokSesudah;
            $stok->save();

            // Update harga per liter BBM jika berubah
            $bbm = BBM::find($mutasiStok->bbm_id);
            if ($bbm) {
                $bbm->harga_per_liter = $mutasiStok->harga_per_liter;
                $bbm->save();
            }

            // Update referensi jika ada
            if ($mutasiStok->referensi_no && $mutasiStok->referensi_type && $mutasiStok->referensi_id) {
                $mutasiStok->referensi_no = null;
                $mutasiStok->referensi_type = null;
                $mutasiStok->referensi_id = null;
                $mutasiStok->save();
            }

            $result = [
                'stok' => $stok,
                'mutasi_stok' => $mutasiStok,
            ];

        } catch (\Throwable $e) {
            Log::error('StockService::rollbackKeluar error: '.$e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * Cek apakah stok cukup untuk jumlah liter yang diminta.
     */
    public static function cekStokCukup(int $bbmId, float $jumlahLiter): bool
    {
        $stok = Stok::with('bbm')->where('bbm_id', $bbmId)->first();

        return $stok && $stok->jumlah >= $jumlahLiter;
    }

    /**
     * Ambil stok yang kritis (di bawah minimum).
     */
    public static function getStokKritis(): array
    {
        $kritis = [];

        foreach (Stok::get() as $stok) {
            if ($stok->isKritis()) {
                $kritis[] = [
                    'id' => $stok->id,
                    'bbm_id' => $stok->bbm_id,
                    'bbm' => $stok->bbm,
                    'jumlah' => $stok->jumlah,
                    'minimum' => $stok->stok_minimum,
                    'maksimum' => $stok->stok_maksimum,
                ];
            }
        }

        return $kritis;
    }
}
