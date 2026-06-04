<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalRequest;
use App\Models\Approval;
use App\Models\Kendaraan;
use App\Models\PO;
use App\Models\Stok;
use App\Models\TransaksiBBM;
use App\Models\Vendor;
use App\Services\ApprovalService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        // Hanya kadiv/admin/super_admin yang bisa akses
        if (! auth()->user()->hasAnyRole(['kadiv', 'admin', 'super_admin'])) {
            abort(403, 'Tidak memiliki akses.');
        }

        $tab = $request->input('tab', 'transaksi');

        $kendaraanList = collect();
        $stokList = collect();
        $vendorList = collect();
        $approvals = collect();

        if ($tab === 'transaksi') {
            $approvals = Approval::where('approvable_type', TransaksiBBM::class)
                ->where('status', 'pending')
                ->with(['approvable.kendaraan.bbm', 'peminta', 'approver'])
                ->orderBy('created_at', 'desc')
                ->get();

            $kendaraanList = Kendaraan::where('status', 'Aktif')
                ->with('bbm')
                ->get();

            $stokList = Stok::with('bbm')->get();
        } elseif ($tab === 'po') {
            $approvals = Approval::where('approvable_type', PO::class)
                ->where('status', 'pending')
                ->with(['approvable.vendor', 'approvable.bbm', 'peminta', 'approver'])
                ->orderBy('created_at', 'desc')
                ->get();

            $vendorList = Vendor::where('status', 'Aktif')
                ->orderBy('nama', 'asc')
                ->limit(50)
                ->get();
        } else {
            abort(404, 'Tab tidak ditemukan.');
        }

        return view('approval.index', compact('approvals', 'kendaraanList', 'stokList', 'vendorList', 'tab'));
    }

    public function proses(ApprovalRequest $request)
    {
        $validated = $request->validated();

        $keputusan = $validated['keputusan'];
        $catatan = $validated['catatan'] ?? '';

        $approval = isset($validated['approval_id'])
            ? Approval::find($validated['approval_id'])
            : Approval::where('status', 'pending')->orderBy('id', 'desc')->first();

        if (! $approval) {
            abort(404, 'Approval tidak ditemukan.');
        }

        DB::beginTransaction();

        try {
            $approval->approver()->associate(auth()->user());
            $approval->save();

            if ($approval->approvable_type === TransaksiBBM::class) {
                ApprovalService::prosesTransaksi($approval->approvable_id, $keputusan, $catatan);
            } elseif ($approval->approvable_type === PO::class) {
                ApprovalService::prosesPO($approval->approvable_id, $keputusan, $catatan);
            } else {
                abort(404, 'Tipe approvable tidak didukung.');
            }

            DB::commit();

            $pesan = $keputusan === 'approved' ? 'Approval berhasil disetujui.' : 'Approval berhasil ditolak.';

            return redirect()->route('approval.index', ['tab' => 'transaksi'])
                ->with('success', $pesan);
        } catch (\Throwable $e) {
            DB::rollBack();

            AuditLogService::log('error', $approval, null, null, null, 'Approval gagal: '.$e->getMessage());

            return redirect()->route('approval.index', ['tab' => 'transaksi'])
                ->with('error', 'Gagal memproses approval: '.$e->getMessage());
        }
    }

    public function riwayat(Request $request)
    {
        $query = Approval::whereIn('status', ['approved', 'rejected'])
            ->orderBy('processed_at', 'desc');

        if ($request->filled('approvable_type')) {
            $query->where('approvable_type', $request->approvable_type);
        }

        $approvals = $query->with(['approvable', 'peminta', 'approver'])->get();

        return response()->json([
            'success' => true,
            'data' => $approvals,
        ]);
    }
}
