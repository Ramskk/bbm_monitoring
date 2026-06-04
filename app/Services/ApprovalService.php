<?php

namespace App\Services;

use App\Models\Approvers;
use App\Models\Approval;
use App\Models\TransaksiBBM;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    /**
     * Proses approval untuk transaksi BBM.
     *
     * @param int $transaksiId
     * @param string $keputusan
     * @param string $catatan
     * @return array
     */
    public function prosesTransaksi(int $transaksiId, string $keputusan, string $catatan = ''): array
    {
        $result = [];

        // Cek role di backend
        $user = auth()->user();
        if (!$user->hasAnyRole(['kadiv', 'admin', 'super_admin'])) {
            throw new \Exception("Tidak memiliki hak akses untuk melakukan approval.");
        }

        $transaksi = TransaksiBBM::with('approvedBy')->find($transaksiId);
        if (!$transaksi) {
            throw new \Exception("Transaksi BBM dengan id {$transaksiId} tidak ditemukan.");
        }

        // Validasi keputusan approval
        if (!in_array($keputusan, ['approved', 'rejected'], true)) {
            throw new \Exception("Keputusan approval tidak valid. Harus 'approved' atau 'rejected'.");
        }

        // Proses approval
        if ($keputusan === 'approved') {
            $transaksi->status = 'approved';
            $transaksi->approved_at = now();
            $transaksi->save();

            // Buat record Approval
            $approval = Approvers::create([
                'approvable_type'  => TransaksiBBM::class,
                'approvable_id'    => $transaksi->id,
                'requested_by'     => $transaksi->user_id,
                'approved_by'      => $user->id,
                'status'           => 'approved',
                'catatan_peminta'  => $transaksi->catatan,
                'catatan_approver' => $catatan,
                'processed_at'     => now(),
            ]);

            $result = [
                'transaksi' => $transaksi,
                'approval' => $approval,
            ];

        } elseif ($keputusan === 'rejected') {
            // Rollback stok
            try {
                $rollback = StockService::rollbackKeluar($transaksi->id);
            } catch (\Exception $e) {
                Log::error('Rollback stok gagal: ' . $e->getMessage());
                throw $e;
            }

            $transaksi->status = 'rejected';
            $transaksi->alasan_reject = $catatan;
            $transaksi->save();

            // Buat record Approval
            $approval = Approvers::create([
                'approvable_type'  => TransaksiBBM::class,
                'approvable_id'    => $transaksi->id,
                'requested_by'     => $transaksi->user_id,
                'approved_by'      => $user->id,
                'status'           => 'rejected',
                'catatan_peminta'  => $transaksi->catatan,
                'catatan_approver' => $catatan,
                'processed_at'     => now(),
            ]);

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
     *
     * @param int $poId
     * @param string $keputusan
     * @param string $catatan
     * @return array
     */
    public function prosesPO(int $poId, string $keputusan, string $catatan = ''): array
    {
        $result = [];

        // Cek role di backend
        $user = auth()->user();
        if (!$user->hasAnyRole(['kadiv', 'admin', 'super_admin'])) {
            throw new \Exception("Tidak memiliki hak akses untuk melakukan approval.");
        }

        $po = \App\Models\PO::with('vendor')->find($poId);
        if (!$po) {
            throw new \Exception("Purchase Order dengan id {$poId} tidak ditemukan.");
        }

        // Validasi status harus draft sebelum diproses
        if ($po->status !== 'draft') {
            throw new \Exception("Purchase Order dengan status '{$po->status}' tidak dapat diproses. Status harus 'draft'.");
        }

        // Validasi keputusan approval
        if (!in_array($keputusan, ['approved', 'rejected'], true)) {
            throw new \Exception("Keputusan approval tidak valid. Harus 'approved' atau 'rejected'.");
        }

        // Proses approval
        if ($keputusan === 'approved') {
            $po->approved_by = $user->id;
            $po->approved_at = now();
            $po->save();

            // Buat record Approval
            $approval = Approvers::create([
                'approvable_type'  => \App\Models\PO::class,
                'approvable_id'    => $po->id,
                'requested_by'     => $po->created_by,
                'approved_by'      => $user->id,
                'status'           => 'approved',
                'catatan_peminta'  => $po->alasan_reject,
                'catatan_approver' => $catatan,
                'processed_at'     => now(),
            ]);

            $result = [
                'po'       => $po,
                'approval' => $approval,
            ];

        } elseif ($keputusan === 'rejected') {
            // Rollback stok jika ada
            if ($po->bbm_id && $po->jumlah_liter > 0) {
                $bbm = \App\Models\BBM::find($po->bbm_id);
                if ($bbm) {
                    try {
                        $rollback = StockService::rollbackKeluar($bbm->stok->id);
                    } catch (\Exception $e) {
                        Log::error('Rollback stok PO gagal: ' . $e->getMessage());
                        throw $e;
                    }
                }
            }

            $po->status = 'rejected';
            $po->alasan_reject = $catatan;
            $po->save();

            // Buat record Approval
            $approval = Approvers::create([
                'approvable_type'  => \App\Models\PO::class,
                'approvable_id'    => $po->id,
                'requested_by'     => $po->created_by,
                'approved_by'      => $user->id,
                'status'           => 'rejected',
                'catatan_peminta'  => $po->alasan_reject,
                'catatan_approver' => $catatan,
                'processed_at'     => now(),
            ]);

            $result = [
                'po'       => $po,
                'rollback' => $rollback,
                'approval' => $approval,
            ];
        }

        return $result;
    }
}
