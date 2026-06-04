<?php

namespace App\Http\Controllers;

use App\Models\BBM;
use App\Models\Stok;
use App\Models\TransaksiBBM;
use App\Models\PO;
use App\Models\MutasiStok;
use App\Http\Requests\StoreBBMRequest;
use Illuminate\Http\Request;

class BBMController extends Controller
{
    public function index(Request $request)
    {
        $query = BBM::whereNull('deleted_at');

        // Filter by jenis
        $jenis = $request->input('jenis');
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        // Filter by is_active
        $aktif = $request->input('is_active');
        if ($aktif !== null) {
            $query->where('is_active', $aktif);
        }

        $bbmList = $query->orderBy('nama', 'asc')
            ->paginate(10);

        return view('bbm.index', compact('bbmList'));
    }

    public function create(Request $request)
    {
        return view('bbm.form');
    }

    public function store(StoreBBMRequest $request)
    {
        $bbm = BBM::create($request->validated());

        // Auto-create record Stok
        Stok::create([
            'bbm_id'    => $bbm->id,
            'jumlah'    => 0,
            'stok_minimum' => $bbm->stok_minimum ?? 0,
            'stok_maksimum' => $bbm->stok_maksimum ?? 0,
            'lokasi'    => $bbm->lokasi ?? '',
        ]);

        AuditLogService::log('create', $bbm, null, null, $request->validated(), 'BBM baru dibuat');

        return redirect()->route('bbm.index')
            ->with('success', 'Jenis BBM berhasil dibuat.');
    }

    public function show(BBM $bbm)
    {
        return view('bbm.show', compact('bbm'));
    }

    public function edit(BBM $bbm)
    {
        return view('bbm.form', compact('bbm'));
    }

    public function update(StoreBBMRequest $request, BBM $bbm)
    {
        $bbm->update($request->validated());

        AuditLogService::log('update', $bbm, null, null, $request->validated(), 'BBM diperbarui');

        return redirect()->route('bbm.index')
            ->with('success', 'Jenis BBM berhasil diperbarui.');
    }

    public function destroy(Request $request, BBM $bbm)
    {
        // Cek transaksi aktif
        if ($bbm->transaksi->count() > 0) {
            abort(403, 'Tidak bisa hapus BBM yang memiliki transaksi aktif.');
        }

        // Cek PO aktif
        if ($bbm->po->count() > 0) {
            abort(403, 'Tidak bisa hapus BBM yang memiliki PO aktif.');
        }

        // Cek stok
        if ($bbm->stok->exists()) {
            abort(403, 'Tidak bisa hapus BBM yang memiliki stok.');
        }

        $bbm->delete();

        AuditLogService::log('delete', $bbm, null, null, null, 'BBM dihapus');

        return redirect()->route('bbm.index')
            ->with('success', 'Jenis BBM berhasil dihapus.');
    }
}
