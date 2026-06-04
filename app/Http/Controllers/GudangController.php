<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\TransaksiBBM;
use App\Models\MutasiStok;
use App\Models\BBM;
use App\Models\PO;
use App\Services\StockService;
use App\Services\AuditLogService;
use App\Models\Approval as ApprovalModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        $stokList = Stok::all();
        $mutasiTerbaru = MutasiStok::latest('tanggal')
            ->whereNotNull('deleted_at')
            ->limit(10)
            ->get();

        $stokKritis = Stok::where('jumlah', '<', Stok::where('bbm_id', 'like', '%_minimum')->first()->jumlah ?? 0)
            ->whereNotNull('deleted_at')
            ->with('bbm')
            ->get();

        $stokPenuh = Stok::where('jumlah', '>=', Stok::where('bbm_id', 'like', '%_maksimum')->first()->jumlah ?? 0)
            ->whereNotNull('deleted_at')
            ->with('bbm')
            ->get();

        return view('gudang.index', compact('stokList', 'mutasiTerbaru', 'stokKritis', 'stokPenuh'));
    }

    public function kartuStok(Request $request)
    {
        $stokId = $request->input('stok_id');
        $dari = $request->input('dari', now()->subDays(30));
        $sampai = $request->input('sampai', now());

        $mutasi = MutasiStok::whereNotNull('deleted_at')
            ->where('tanggal', '>=', $dari)
            ->where('tanggal', '<=', $sampai)
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $mutasi,
        ]);
    }

    public function formStokMasuk(Request $request)
    {
        $bbmList = BBM::whereNotNull('deleted_at')
            ->where('is_active', true)
            ->with('stok')
            ->get();

        return view('gudang.stok_masuk', compact('bbmList'));
    }

    public function stokMasuk(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate($request->route()->getValidatorInstance());

            $bbm = BBM::find($validated['bbm_id']);
            $stok = Stok::find($bbm->id);

            // Cek stok tidak melebihi kapasitas maksimum
            if ($bbm->stok_maksimum && $validated['jumlah'] > $bbm->stok_maksimum) {
                throw new \Exception("Jumlah melebihi kapasitas maksimum {$bbm->stok_maksimum} liter.");
            }

            // Tambah stok via service
            StockService::tambahStok(
                $bbm->id,
                $validated['jumlah'],
                $validated['harga_per_liter'] ?? $bbm->harga_per_liter,
                $validated['referensi_no'] ?? null,
                $validated['referensi_type'] ?? null,
                $validated['referensi_id'] ?? null
            );

            AuditLogService::log('create', $stok, null, null, null, 'Stok masuk ditambahkan');

            DB::commit();

            return redirect()->route('gudang.index')
                ->with('success', 'Stok masuk berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();

            AuditLogService::log('error', $stok ?? null, null, null, null, 'Stok masuk gagal: ' . $e->getMessage());

            return redirect()->route('gudang.index')
                ->with('error', 'Gagal menambahkan stok masuk.');
        }
    }

    public function updateBatasStok(Request $request, Stok $stok)
    {
        $validated = $request->validate($request->route()->getValidatorInstance());

        $stok->update([
            'stok_minimum' => $validated['stok_minimum'],
            'stok_maksimum' => $validated['stok_maksimum'],
            'lokasi' => $validated['lokasi'] ?? $stok->lokasi,
        ]);

        AuditLogService::log('update', $stok, null, ['stok_minimum' => $stok->stok_minimum, 'stok_maksimum' => $stok->stok_maksimum, 'lokasi' => $stok->lokasi], $validated, 'Stok batas diperbarui');

        return redirect()->route('gudang.index')
            ->with('success', 'Stok batas berhasil diperbarui.');
    }
}
