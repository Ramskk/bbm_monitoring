<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePORequest;
use App\Models\Approval as ApprovalModel;
use App\Models\BBM;
use App\Models\PO;
use App\Models\Vendor;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POController extends Controller
{
    public function index(Request $request)
    {
        $query = PO::with(['vendor', 'bbm']);

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

        $pos = $query->orderBy('tanggal_po', 'desc')
            ->paginate(10);

        $poList = $pos;

        $vendors = Vendor::where('status', 'Aktif')
            ->orderBy('nama', 'asc')
            ->limit(50)
            ->get();

        $vendorList = $vendors;

        return view('po.index', compact('poList', 'pos', 'vendorList', 'vendors'));
    }

    public function create(Request $request)
    {
        $vendorList = Vendor::where('status', 'Aktif')
            ->orderBy('nama', 'asc')
            ->limit(50)
            ->get();

        $bbmList = BBM::where('is_active', true)
            ->with('stok')
            ->get();

        return view('po.create', compact('vendorList', 'bbmList'));
    }

    public function store(StorePORequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $totalNilai = (float) $validated['jumlah_liter'] * (float) $validated['harga_per_liter'];

            // Buat record PO
            $po = PO::create([
                'no_po' => PO::generateNoPO(),
                'vendor_id' => $validated['vendor_id'],
                'bbm_id' => $validated['bbm_id'],
                'jumlah_liter' => $validated['jumlah_liter'],
                'harga_per_liter' => $validated['harga_per_liter'],
                'total_nilai' => $totalNilai,
                'tanggal_po' => $validated['tanggal_po'],
                'tanggal_kirim_rencana' => $validated['tanggal_kirim_rencana'] ?? null,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Buat record Approval
            ApprovalModel::create([
                'approvable_type' => PO::class,
                'approvable_id' => $po->id,
                'requested_by' => auth()->id(),
                'status' => 'pending',
            ]);

            AuditLogService::log('create', $po, null, null, null, 'PO dibuat');

            DB::commit();

            return redirect()->route('po.index')
                ->with('success', 'PO berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('po.index')
                ->with('error', 'Gagal membuat PO: '.$e->getMessage());
        }
    }

    public function show(PO $po)
    {
        $po->load(['vendor', 'bbm', 'approval']);
        $approval = $po->approval;

        return view('po.show', compact('po', 'approval'));
    }

    public function updateStatus(Request $request, PO $po)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        // Validasi lifecycle status
        if (! $po->canTransitionTo($validated['status'])) {
            abort(403, 'Status tidak valid. PO tidak bisa melompat status.');
        }

        $before = ['status' => $po->status];

        $po->update([
            'status' => $validated['status'],
            'approved_by' => $request->input('approved_by'),
            'approved_at' => $request->input('approved_at'),
        ]);

        AuditLogService::log('update', $po, null, $before, $validated, 'PO status diubah');

        return redirect()->route('po.index')
            ->with('success', 'PO berhasil diperbarui.');
    }

    public function close(Request $request, PO $po)
    {
        $before = ['status' => $po->status];

        $po->update([
            'status' => 'closed',
            'approved_by' => $request->input('approved_by'),
            'approved_at' => $request->input('approved_at'),
        ]);

        AuditLogService::log('update', $po, null, $before, ['status' => 'closed'], 'PO ditutup');

        return redirect()->route('po.index')
            ->with('success', 'PO berhasil ditutup.');
    }
}
