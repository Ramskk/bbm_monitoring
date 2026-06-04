<?php

namespace App\Http\Controllers;

use App\Models\TransaksiBBM;
use App\Models\PO;
use App\Models\Approval;
use App\Services\ApprovalService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        // Hanya kadiv/admin/super_admin yang bisa akses
        if (!auth()->user()->hasAnyRole(['kadiv', 'admin', 'super_admin'])) {
            abort(403, 'Tidak memiliki akses.');
        }

        $tab = $request->input('tab', 'pending');
        
        $kendaraanList = [];
        $stokList = [];
        $vendorList = [];

        if ($tab === 'transaksi') {
            $query = Approval::where('approvable_type', TransaksiBBM::class)
                ->where('status', 'pending')
                ->orderBy('tanggal_pemakaian', 'desc');

            if ($request->filled('kendaraan_id')) {
                $query->join('kendaraans', 'approvable_id', '=', 'kendaraans.id');
                $query->where('kendaraans.id', $request->input('kendaraan_id'));
            }

            $approvals = $query->with(['approvable.kendaraans.bbm', 'peminta', 'approver'])
                ->get();

            $kendaraanList = Kendaraan::whereNotNull('deleted_at')
                ->where('is_active', true)
                ->with('bbm')
                ->get();

            $stokList = Stok::all();

        } elseif ($tab === 'po') {
            $query = Approval::where('approvable_type', PO::class)
                ->where('status', 'pending')
                ->orderBy('tanggal_po', 'desc');

            if ($request->filled('vendor_id')) {
                $query->join('vendors', 'approvable_id', '=', 'vendors.id');
                $query->where('vendors.id', $request->input('vendor_id'));
            }

            $approvals = $query->with(['approvable.vendor', 'peminta', 'approver'])
                ->get();

            $vendorList = Vendor::whereNotNull('deleted_at')
                ->where('is_active', true)
                ->with(['po' => function ($q) {
                    $q->orderBy('tanggal_po', 'desc');
                }])
                ->limit(50)
                ->get();

        } else {
            abort(404, 'Tab tidak ditemukan.');
        }

        return view('approval.index', compact('approvals', 'kendaraanList', 'stokList', 'vendorList'));
    }

    public function proses(Request $request)
    {
        $request->validate($request->route()->getValidatorInstance());

        $keputusan = $request->keputusan;
        $catatan = $request->catatan;

        // Proses berdasarkan jenis approvable
        if ($keputusan === 'approved') {
            $approval = Approval::where('status', 'pending')
                ->orderBy('id', 'desc')
                ->first();

            if (!$approval) {
                abort(404, 'Approval tidak ditemukan.');
            }

            DB::beginTransaction();

            try {
                if ($approval->approvable_type === TransaksiBBM::class) {
                    $transaksi = TransaksiBBM::find($approval->approvable_id);
                    $approval->approver()->associate(auth()->user());
                    $approval->save();

                    ApprovalService::prosesTransaksi($transaksi->id, 'approved', $catatan);

                } elseif ($approval->approvable_type === PO::class) {
                    $po = PO::find($approval->approvable_id);
                    $approval->approver()->associate(auth()->user());
                    $approval->save();

                    ApprovalService::prosesPO($po->id, 'approved', $catatan);

                } else {
                    abort(404, 'Tipo approvable tidak didukung.');
                }

                DB::commit();

                return redirect()->route('approval.index', ['tab' => 'pending'])
                    ->with('success', 'Approval berhasil disetujui.');

            } catch (\Exception $e) {
                DB::rollBack();

                AuditLogService::log('error', $approval ?? null, null, null, null, 'Approval gagal: ' . $e->getMessage());

                return redirect()->route('approval.index', ['tab' => 'pending'])
                    ->with('error', 'Gagal memproses approval.');
            }

        } elseif ($keputusan === 'rejected') {
            $approval = Approval::where('status', 'pending')
                ->orderBy('id', 'desc')
                ->first();

            if (!$approval) {
                abort(404, 'Approval tidak ditemukan.');
            }

            DB::beginTransaction();

            try {
                if ($approval->approvable_type === TransaksiBBM::class) {
                    $transaksi = TransaksiBBM::find($approval->approvable_id);
                    $approval->approver()->associate(auth()->user());
                    $approval->save();

                    ApprovalService::prosesTransaksi($transaksi->id, 'rejected', $catatan);

                } elseif ($approval->approvable_type === PO::class) {
                    $po = PO::find($approval->approvable_id);
                    $approval->approver()->associate(auth()->user());
                    $approval->save();

                    ApprovalService::prosesPO($po->id, 'rejected', $catatan);

                } else {
                    abort(404, 'Tipo approvable tidak didukung.');
                }

                DB::commit();

                return redirect()->route('approval.index', ['tab' => 'pending'])
                    ->with('success', 'Approval berhasil ditolak.');

            } catch (\Exception $e) {
                DB::rollBack();

                AuditLogService::log('error', $approval ?? null, null, null, null, 'Approval gagal: ' . $e->getMessage());

                return redirect()->route('approval.index', ['tab' => 'pending'])
                    ->with('error', 'Gagal memproses approval.');
            }
        } else {
            abort(400, 'Keputusan tidak valid.');
        }
    }

    public function riwayat(Request $request)
    {
        $query = Approval::whereNotNull('deleted_at')
            ->where('status', 'approved')
            ->orderBy('processed_at', 'desc');

        if ($request->filled('approvable_type')) {
            $query->where('approvable_type', $request->approvable_type);
        }

        $approvals = $query->with(['approvable', 'peminta', 'approver'])
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $approvals,
        ]);
    }
}
