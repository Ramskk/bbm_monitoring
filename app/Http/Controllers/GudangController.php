<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStokMasukRequest;
use App\Models\BBM;
use App\Models\MutasiStok;
use App\Models\Stok;
use App\Services\AuditLogService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        $stokList = Stok::with('bbm')->get();

        $mutasiTerbaru = MutasiStok::with('bbm')
            ->latest('tanggal')
            ->limit(10)
            ->get();

        $stokKritis = Stok::whereColumn('jumlah', '<=', 'stok_minimum')
            ->with('bbm')
            ->get();

        $stokPenuh = Stok::whereColumn('jumlah', '>=', 'stok_maksimum')
            ->with('bbm')
            ->get();

        return view('gudang.index', compact('stokList', 'mutasiTerbaru', 'stokKritis', 'stokPenuh'));
    }

    public function kartuStok(Request $request)
    {
        $stokId = $request->input('stok_id');
        $dari = $request->input('dari', now()->subDays(30));
        $sampai = $request->input('sampai', now());

        $query = MutasiStok::with('bbm')
            ->where('tanggal', '>=', $dari)
            ->where('tanggal', '<=', $sampai);

        if ($stokId) {
            $query->where('stok_id', $stokId);
        }

        $mutasi = $query->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $mutasi,
        ]);
    }

    public function formStokMasuk(Request $request)
    {
        $bbmList = BBM::where('is_active', true)
            ->with('stok')
            ->get();

        return view('gudang.stok_masuk', compact('bbmList'));
    }

    public function stokMasuk(StoreStokMasukRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();

            $bbm = BBM::findOrFail($validated['bbm_id']);

            // Tambah stok via service
            StockService::tambahStok(
                $bbm->id,
                (float) $validated['jumlah'],
                (float) ($request->input('harga_per_liter') ?? $bbm->harga_per_liter),
                (string) ($request->input('referensi_no') ?? ''),
                (string) ($request->input('referensi_type') ?? 'manual'),
                (int) ($request->input('referensi_id') ?? 0)
            );

            $stok = Stok::where('bbm_id', $bbm->id)->first();

            AuditLogService::log('create', $stok, null, null, null, 'Stok masuk ditambahkan');

            DB::commit();

            return redirect()->route('gudang.index')
                ->with('success', 'Stok masuk berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('gudang.index')
                ->with('error', 'Gagal menambahkan stok masuk: '.$e->getMessage());
        }
    }

    public function updateBatasStok(Request $request, Stok $stok)
    {
        $validated = $request->validate([
            'stok_minimum' => 'required|numeric|min:0',
            'stok_maksimum' => 'required|numeric|gte:stok_minimum',
            'lokasi' => 'nullable|string|max:255',
        ]);

        $before = [
            'stok_minimum' => $stok->stok_minimum,
            'stok_maksimum' => $stok->stok_maksimum,
            'lokasi' => $stok->lokasi,
        ];

        $stok->update([
            'stok_minimum' => $validated['stok_minimum'],
            'stok_maksimum' => $validated['stok_maksimum'],
            'lokasi' => $validated['lokasi'] ?? $stok->lokasi,
        ]);

        AuditLogService::log('update', $stok, null, $before, $validated, 'Stok batas diperbarui');

        return redirect()->route('gudang.index')
            ->with('success', 'Stok batas berhasil diperbarui.');
    }
}
