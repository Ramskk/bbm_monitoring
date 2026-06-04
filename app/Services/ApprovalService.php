<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\BBM;
use App\Models\MutasiStok;
use App\Models\PO;
use App\Models\TransaksiBBM;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    /**
     * Proses approval untuk transaksi BBM.
     */
    public static function prosesTransaksi(int $transaksiId, string $keputusan, string $catatan = ''): array
    {
        $result = [];

        // Cek role di backend
        $user = auth()->user();
        if (! $user->hasAnyRole(['kadiv', 'admin', 'super_admin'])) {
            throw new \Exception('Tidak memiliki hak akses untuk melakukan approval.');
        }

        $transaksi = TransaksiBBM::with('approvedBy')->find($transaksiId);
        if (! $transaksi) {
            throw new \Exception("Transaksi BBM dengan id {$transaksiId} tidak ditemukan.");
        }

        // Validasi keputusan approval
        if (! in_array($keputusan, ['approved', 'rejected'], true)) {
            throw new \Exception("Keputusan approval tidak valid. Harus 'approved' atau 'rejected'.");
        }

        // Proses approval
        if ($keputusan === 'approved') {
            $transaksi->status = 'approved';
            $transaksi->approved_at = now();
            $transaksi->save();

            // Update record Approval yang masih pending
            $approval = Approval::updateOrCreate(
                [
                    'approvable_type' => TransaksiBBM::class,
                    'approvable_id' => $transaksi->id,
                    'status' => 'pending',
                ],
                [
                    'requested_by' => $transaksi->user_id,
                    'approved_by' => $user->id,
                    'status' => 'approved',
                    'catatan_approver' => $catatan,
                    'processed_at' => now(),
                ]
            );

            $result = [
                'transaksi' => $transaksi,
                'approval' => $approval,
            ];

        } elseif ($keputusan === 'rejected') {
            // Rollback stok berdasarkan mutasi keluar dari transaksi ini
            $rollback = null;
            $mutasi = MutasiStok::where('referensi_type', 'Transaksi')
                ->where('referensi_id', $transaksi->id)
                ->where('jenis', 'keluar')
                ->latest('id')
                ->first();

            if ($mutasi) {
                try {
                    $rollback = StockService::rollbackKeluar($mutasi->id);
                } catch (\Throwable $e) {
                    Log::error('Rollback stok gagal: '.$e->getMessage());
                    throw $e;
                }
            }

            $transaksi->status = 'rejected';
            $transaksi->alasan_reject = $catatan;
            $transaksi->save();

            // Update record Approval yang masih pending
            $approval = Approval::updateOrCreate(
                [
                    'approvable_type' => TransaksiBBM::class,
                    'approvable_id' => $transaksi->id,
                    'status' => 'pending',
                ],
                [
                    'requested_by' => $transaksi->user_id,
                    'approved_by' => $user->id,
                    'status' => 'rejected',
                    'catatan_approver' => $catatan,
                    'processed_at' => now(),
                ]
            );

            $result = [
                'transaksi' => $transaksi,
                'rollback' => $rollback,
                'approval' => $approval,
            ];
        }

        return $result;
    }

    /**
     * Proses approval untuk Purchase Order.
     */
    public static function prosesPO(int $poId, string $keputusan, string $catatan = ''): array
    {
        $result = [];

        // Cek role di backend
        $user = auth()->user();
        if (! $user->hasAnyRole(['kadiv', 'admin', 'super_admin'])) {
            throw new \Exception('Tidak memiliki hak akses untuk melakukan approval.');
        }

        $po = PO::with('vendor')->find($poId);
        if (! $po) {
            throw new \Exception("Purchase Order dengan id {$poId} tidak ditemukan.");
        }

        // Validasi status harus draft sebelum diproses
        if ($po->status !== 'draft') {
            throw new \Exception("Purchase Order dengan status '{$po->status}' tidak dapat diproses. Status harus 'draft'.");
        }

        // Validasi keputusan approval
        if (! in_array($keputusan, ['approved', 'rejected'], true)) {
            throw new \Exception("Keputusan approval tidak valid. Harus 'approved' atau 'rejected'.");
        }

        // Proses approval
        if ($keputusan === 'approved') {
            $po->status = 'approved';
            $po->approved_by = $user->id;
            $po->approved_at = now();
            $po->save();

            // Update record Approval yang masih pending
            $approval = Approval::updateOrCreate(
                [
                    'approvable_type' => PO::class,
                    'approvable_id' => $po->id,
                    'status' => 'pending',
                ],
                [
                    'requested_by' => $po->created_by,
                    'approved_by' => $user->id,
                    'status' => 'approved',
                    'catatan_approver' => $catatan,
                    'processed_at' => now(),
                ]
            );

            $result = [
                'po' => $po,
                'approval' => $approval,
            ];

        } elseif ($keputusan === 'rejected') {
            // PO yang ditolak tidak mengurangi stok, jadi tidak ada rollback stok
            $rollback = null;

            $po->status = 'rejected';
            $po->alasan_reject = $catatan;
            $po->save();

            // Update record Approval yang masih pending
            $approval = Approval::updateOrCreate(
                [
                    'approvable_type' => PO::class,
                    'approvable_id' => $po->id,
                    'status' => 'pending',
                ],
                [
                    'requested_by' => $po->created_by,
                    'approved_by' => $user->id,
                    'status' => 'rejected',
                    'catatan_approver' => $catatan,
                    'processed_at' => now(),
                ]
            );

            $result = [
                'po' => $po,
                'rollback' => $rollback,
                'approval' => $approval,
            ];
        }

        return $result;
    }
}
