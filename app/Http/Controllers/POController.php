<?php

namespace App\Http\Controllers;

use App\Models\PO;
use App\Models\Vendor;
use App\Models\BBM;
use App\Models\Stok;
use App\Services\StockService;
use App\Services\AuditLogService;
use App\Models\Approval as ApprovalModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POController extends Controller
{
    public function index(Request $request)
    {
        $query = PO::whereNull('deleted_at');

        // Filter by status
        $status = $request->input('status');
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by vendor
        $vendorId = $request->input('vendor_id');
        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        $poList = $query->orderBy('tanggal_po', 'desc')
            ->paginate(10);

        $vendorList = Vendor::whereNull('deleted_at')
            ->where('is_active', true)
            ->with('po', function ($q) {
                $q->orderBy('tanggal_po', 'desc');
            })
            ->limit(50)
            ->get();

        return view('po.index', compact('poList', 'vendorList'));
    }

    public function create(Request $request)
    {
        $vendorList = Vendor::whereNotNull('deleted_at')
            ->where('is_active', true)
            ->with(['po' => function ($q) {
                $q->orderBy('tanggal_po', 'desc');
            }])
            ->limit(50)
            ->get();

        $bbmList = BBM::whereNotNull('deleted_at')
            ->where('is_active', true)
            ->with('stok')
            ->get();

        return view('po.create', compact('vendorList', 'bbmList'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate($request->route()->getValidatorInstance());

            // Buat record PO
            $po = PO::create([
                'no_po'                  => PO::generateNoPO(),
                'vendor_id'              => $validated['vendor_id'],
                'bbm_id'                 => $validated['bbm_id'],
                'jumlah_liter'           => $validated['jumlah_liter'],
                'harga_per_liter'        => $validated['harga_per_liter'],
                'total_nilai'            => $validated['total_nilai'],
                'tanggal_po'             => $validated['tanggal_po'],
                'tanggal_kirim_rencana'  => $validated['tanggal_kirim_rencana'],
                'status'                 => 'draft',
                'created_by'             => auth()->id(),
            ]);

            // Buat record Approval
            ApprovalModel::create([
                'approvable_type'   => PO::class,
                'approvable_id'     => $po->id,
                'requested_by'      => auth()->id(),
                'status'            => 'pending',
            ]);

            AuditLogService::log('create', $po, null, null, null, 'PO dibuat');

            DB::commit();

            return redirect()->route('po.index')
                ->with('success', 'PO berhasil dibuat.')
                ->withInput($request->except('id'));

        } catch (\Exception $e) {
            DB::rollBack();

            AuditLogService::log('error', $po ?? null, null, null, null, 'PO gagal dibuat: ' . $e->getMessage());

            return redirect()->route('po.index')
                ->with('error', 'Gagal membuat PO.');
        }
    }

    public function show(PO $po)
    {
        $approval = $po->approval;

        return view('po.show', compact('po', 'approval'));
    }

    public function updateStatus(Request $request, PO $po)
    {
        $validated = $request->validate($request->route()->getValidatorInstance());

        // Validasi lifecycle status
        if (!$po->canTransitionTo($validated['status'])) {
            abort(403, 'Status tidak valid. PO tidak bisa melompat status.');
        }

        $po->update([
            'status'              => $validated['status'],
            'approved_by'         => $validated['approved_by'] ?? null,
            'approved_at'         => $validated['approved_at'] ?? null,
        ]);

        AuditLogService::log('update', $po, null, ['status' => $po->status], $validated, 'PO status diubah');

        return redirect()->route('po.index')
            ->with('success', 'PO berhasil diperbarui.');
    }

    public function close(Request $request, PO $po)
    {
        $validated = $request->validate($request->route()->getValidatorInstance());

        $po->update([
            'status' => 'closed',
            'approved_by' => $validated['approved_by'] ?? null,
            'approved_at' => $validated['approved_at'] ?? null,
        ]);

        AuditLogService::log('update', $po, null, ['status' => $po->status], $validated, 'PO ditutup');

        return redirect()->route('po.index')
            ->with('success', 'PO berhasil ditutup.');
    }
}
