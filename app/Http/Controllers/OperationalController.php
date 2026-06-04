<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePemakaianRequest;
use App\Models\Approval as ApprovalModel;
use App\Models\BBM;
use App\Models\Kendaraan;
use App\Models\Stok;
use App\Models\TransaksiBBM;
use App\Services\AuditLogService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $kendaraanId = $request->input('kendaraan_id');
        $dari = $request->input('dari', now()->subDays(30));
        $sampai = $request->input('sampai', now());

        $query = TransaksiBBM::with(['kendaraan.bbm', 'bbm'])
            ->whereBetween('tanggal_pemakaian', [$dari, $sampai]);

        if ($status) {
            $query->where('status', $status);
        }

        if ($kendaraanId) {
            $query->where('kendaraan_id', $kendaraanId);
        }

        // Non-admin hanya lihat transaksi sendiri
        if (! auth()->user()->hasAnyRole(['admin', 'super_admin'])) {
            $query->where('user_id', auth()->id());
        }

        $transaksi = $query->orderBy('tanggal_pemakaian', 'desc')
            ->paginate(10);

        $kendaraanList = Kendaraan::where('status', 'Aktif')
            ->with('bbm')
            ->get();

        $stokList = Stok::with('bbm')->get();

        return view('operational.index', compact(
            'transaksi', 'kendaraanList', 'stokList',
            'dari', 'sampai', 'status'
        ));
    }

    public function create(Request $request)
    {
        $kendaraanList = Kendaraan::where('status', 'Aktif')
            ->with('bbm')
            ->get();

        $stokList = Stok::with('bbm')->get();

        return view('operational.create', compact('kendaraanList', 'stokList'));
    }

    public function store(StorePemakaianRequest $request)
    {
        $validated = $request->validated();

        $kendaraan = Kendaraan::findOrFail($validated['kendaraan_id']);
        $bbm = BBM::findOrFail($kendaraan->bbm_id);
        $stok = Stok::where('bbm_id', $bbm->id)->first();

        DB::beginTransaction();

        try {
            $odometerSebelum = (float) $kendaraan->odometer_terakhir;
            $jarakTempuh = (float) $validated['odometer_sesudah'] - $odometerSebelum;
            $jumlahLiter = (float) $validated['jumlah_liter'];
            $efisiensi = $jumlahLiter > 0 ? round($jarakTempuh / $jumlahLiter, 2) : 0;

            // Buat record TransaksiBBM (status: pending)
            $transaksi = TransaksiBBM::create([
                'no_transaksi' => TransaksiBBM::generateNoTransaksi(),
                'kendaraan_id' => $kendaraan->id,
                'bbm_id' => $bbm->id,
                'stok_id' => $stok?->id,
                'user_id' => auth()->id(),
                'jumlah_liter' => $jumlahLiter,
                'harga_per_liter' => $bbm->harga_per_liter,
                'total_biaya' => $jumlahLiter * $bbm->harga_per_liter,
                'odometer_sebelum' => $odometerSebelum,
                'odometer_sesudah' => $validated['odometer_sesudah'],
                'jarak_tempuh' => $jarakTempuh,
                'efisiensi' => $efisiensi,
                'status' => 'pending',
                'tanggal_pemakaian' => $validated['tanggal_pemakaian'],
                'lokasi_pengisian' => $request->input('lokasi_pengisian', ''),
            ]);

            // Kurangi stok via service
            StockService::kurangiStok(
                $bbm->id,
                $jumlahLiter,
                $transaksi->no_transaksi,
                'Transaksi',
                $transaksi->id,
                'Pemakaian BBM kendaraan '.$kendaraan->nomor_polisi
            );

            // Update odometer
            $kendaraan->update(['odometer_terakhir' => $validated['odometer_sesudah']]);

            // Buat record Approval
            ApprovalModel::create([
                'approvable_type' => TransaksiBBM::class,
                'approvable_id' => $transaksi->id,
                'requested_by' => auth()->id(),
                'status' => 'pending',
            ]);

            AuditLogService::log('create', $transaksi, null, null, null, 'Transaksi BBM dibuat');

            DB::commit();

            return redirect()->route('operational.index')
                ->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('operational.index')
                ->with('error', 'Gagal membuat transaksi: '.$e->getMessage());
        }
    }

    public function show(TransaksiBBM $transaksi)
    {
        $transaksi->load(['kendaraan.bbm', 'bbm', 'approval']);
        $approval = $transaksi->approval;

        return view('operational.show', compact('transaksi', 'approval'));
    }

    public function monitoringOdometer(Request $request)
    {
        $kendaraanList = Kendaraan::where('status', 'Aktif')
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

        $kendaraan = Kendaraan::with('bbm')->find($id);

        if (! $kendaraan) {
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
