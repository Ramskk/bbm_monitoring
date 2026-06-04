<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\BBM;
use App\Models\Stok;
use App\Models\TransaksiBBM;
use App\Models\Approval;
use App\Models\MutasiStok;
use App\Http\Requests\StoreKendaraanRequest;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::whereNull('deleted_at');

        // Filter by status
        $status = $request->input('status');
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by departemen
        $departemen = $request->input('departemen');
        if ($departemen) {
            $query->where('departemen', $departemen);
        }

        // Filter by bbm_id
        $bbmId = $request->input('bbm_id');
        if ($bbmId) {
            $query->where('bbm_id', $bbmId);
        }

        // Non-admin hanya lihat kendaraan sendiri
        if (!auth()->user()->hasAnyRole(['admin', 'super_admin'])) {
            $query->where('departemen', auth()->user()->departemen);
        }

        $kendaraanList = $query->orderBy('nomor_polisi', 'asc')
            ->paginate(10);

        return view('kendaraan.index', compact('kendaraanList'));
    }

    public function create(Request $request)
    {
        $bbmList = BBM::whereNotNull('deleted_at')
            ->where('is_active', true)
            ->with('stok')
            ->get();

        return view('kendaraan.form', compact('bbmList'));
    }

    public function store(StoreKendaraanRequest $request)
    {
        Kendaraan::create($request->validated());

        return redirect()->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil dibuat.');
    }

    public function show(Kendaraan $kendaraan)
    {
        $transaksi = $kendaraan->transaksi
            ->where('tanggal_pemakaian', '>=', now()->subDays(30))
            ->where('tanggal_pemakaian', '<=', now())
            ->orderBy('tanggal_pemakaian', 'desc')
            ->get();

        $mutasi = $kendaraan->transaksi
            ->where('tanggal_pemakaian', '>=', now()->subMonths(3))
            ->where('tanggal_pemakaian', '<=', now())
            ->with('approval')
            ->get();

        return view('kendaraan.show', compact('kendaraan', 'transaksi', 'mutasi'));
    }

    public function edit(Kendaraan $kendaraan)
    {
        return view('kendaraan.form', compact('kendaraan'));
    }

    public function update(StoreKendaraanRequest $request, Kendaraan $kendaraan)
    {
        $kendaraan->update($request->validated());

        return redirect()->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Request $request, Kendaraan $kendaraan)
    {
        // Cek transaksi pending
        $pendingTransaksi = TransaksiBBM::where('kendaraan_id', $kendaraan->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingTransaksi) {
            abort(403, 'Tidak bisa hapus kendaraan yang memiliki transaksi pending.');
        }

        $kendaraan->delete();

        AuditLogService::log('delete', $kendaraan, null, null, null, 'Kendaraan dihapus');

        return redirect()->route('kendaraan.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}
