<?php

namespace App\Http\Controllers;

use App\Models\TransaksiBBM;
use App\Models\Kendaraan;
use App\Models\Stok;
use App\Models\BBM;
use App\Models\Approval;
use App\Services\StockService;
use App\Services\AuditLogService;
use App\Models\Approval as ApprovalModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationalController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaksiBBM::whereNull('deleted_at')
            ->join('kendaraans', 'transaksi_bbm.kendaraan_id', '=', 'kendaraans.id')
            ->join('bbms', 'kendaraans.bbmid', '=', 'bbms.id');

        // Filter by status
        $status = $request->input('status');
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by kendaraan
        $kendaraanId = $request->input('kendaraan_id');
        if ($kendaraanId) {
            $query->where('kendaraans.id', $kendaraanId);
        }

        // Filter by tanggal
        $dari = $request->input('dari', now()->subDays(30));
        $sampai = $request->input('sampai', now());
        $query->whereBetween('tanggal_pemakaian', [$dari, $sampai]);

        // Non-admin hanya lihat transaksi sendiri
        if (!auth()->user()->hasAnyRole(['admin', 'super_admin'])) {
            $query->where('user_id', auth()->id());
        }

        $transaksi = $query->orderBy('tanggal_pemakaian', 'desc')
            ->paginate(10);

        $kendaraanList = Kendaraan::whereNotNull('deleted_at')
            ->where('is_active', true)
            ->with('bbm')
            ->get();

        $stokList = Stok::all();

        return view('operational.index', compact(
            'transaksi', 'kendaraanList', 'stokList',
            'dari', 'sampai', 'status'
        ));
    }

    public function create(Request $request)
    {
        $kendaraanList = Kendaraan::whereNotNull('deleted_at')
            ->where('is_active', true)
            ->with('bbm')
            ->get();

        $stokList = Stok::all();

        return view('operational.create', compact('kendaraanList', 'stokList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($request->route()->getValidatorInstance());

        $kendaraan = Kendaraan::find($validated['kendaraan_id']);
        $bbm = BBM::find($kendaraan->bbm_id);
        $stok = Stok::find($bbm->id);

        DB::beginTransaction();

        try {
            // Buat record TransaksiBBM (status: pending)
            $transaksi = TransaksiBBM::create([
                'no_transaksi'       => TransaksiBBM::generateNoTransaksi(),
                'kendaraan_id'        => $validated['kendaraan_id'],
                'bbm_id'              => $bbm->id,
                'stok_id'             => $stok->id,
                'user_id'             => auth()->id(),
                'jumlah_liter'        => $validated['jumlah_liter'],
                'harga_per_liter'     => $bbm->harga_per_liter,
                'total_biaya'         => $validated['jumlah_liter'] * $bbm->harga_per_liter,
                'odometer_sebelum'   => $kendaraan->odometer_terakhir,
                'odometer_sesudah'   => $validated['odometer_sesudah'],
                'jarak_tempuh'        => $validated['odometer_sesudah'] - $kendaraan->odometer_terakhir,
                'efisiensi'           => round($kendaraan->kapasitas_tangki / $validated['jumlah_liter'], 2),
                'status'              => 'pending',
                'tanggal_pemakaian'   => $validated['tanggal_pemakaian'],
                'lokasi_pengisian'    => $validated['lokasi_pengisian'] ?? '',
            ]);

            // Kurangi stok via service
            StockService::kurangiStok(
                $stok->id,
                $validated['jumlah_liter'],
                $transaksi->no_transaksi,
                'Transaksi',
                $transaksi->id,
                $validated['lokasi_pengisian'] ?? '',
                $bbm->harga_per_liter
            );

            // Update odometer
            $kendaraan->updateOdometer($validated['odometer_sesudah']);

            // Buat record Approval
            ApprovalModel::create([
                'approvable_type'   => TransaksiBBM::class,
                'approvable_id'     => $transaksi->id,
                'requested_by'      => auth()->id(),
                'status'            => 'pending',
            ]);

            AuditLogService::log('create', $transaksi, null, null, null, 'Transaksi BBM dibuat');

            DB::commit();

            return redirect()->route('operational.index')
                ->with('success', 'Transaksi berhasil dibuat.')
                ->withInput($request->except('id'));

        } catch (\Exception $e) {
            DB::rollBack();

            AuditLogService::log('error', $transaksi ?? null, null, null, null, 'Transaksi gagal: ' . $e->getMessage());

            return redirect()->route('operational.index')
                ->with('error', 'Gagal membuat transaksi.')
                ->withInput($request->except('id'));
        }
    }

    public function show(TransaksiBBM $transaksi)
    {
        $approval = $transaksi->approval;

        return view('operational.show', compact('transaksi', 'approval'));
    }

    public function monitoringOdometer(Request $request)
    {
        $kendaraanList = Kendaraan::whereNotNull('deleted_at')
            ->where('is_active', true)
            ->with([
                'bbm',
                'transaksi' => function ($q) use ($request) {
                    $q->where('tanggal_pemakaian', '>=', $request->input('dari', now()->startOfMonth()))
                      ->where('tanggal_pemakaian', '<=', $request->input('sampai', now()->endOfMonth()))
                      ->orderBy('tanggal_pemakaian', 'desc');
                },
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $kendaraanList,
        ]);
    }

    public function getKendaraanInfo(Request $request)
    {
        $id = $request->input('id');

        $kendaraan = Kendaraan::where('id', $id)
            ->whereNotNull('deleted_at')
            ->first();

        if (!$kendaraan) {
            return response()->json([
                'success' => false,
                'message' => 'Kendaraan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kendaraan,
        ]);
    }
}
