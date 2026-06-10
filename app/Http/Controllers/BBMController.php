<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBBMRequest;
use App\Models\BBM;
use App\Models\PO;
use App\Models\Stok;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class BBMController extends Controller
{
    public function index(Request $request)
    {
        $query = BBM::query();

        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter by is_active
        if ($request->has('is_active') && $request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        $bbmList = $query->orderBy('nama', 'asc')
            ->paginate(10);

        return view('bbm.index', compact('bbmList'));
    }

    public function create()
    {
        return view('bbm.form');
    }

    public function store(StoreBBMRequest $request)
    {
        $data = $request->validated();

        // ✅ FIX UTAMA: AUTO GENERATE KODE
        $last = BBM::orderBy('id', 'desc')->first();

        $nextNumber = 1;

        if ($last && $last->kode) {
            $number = (int) substr($last->kode, 4);
            $nextNumber = $number + 1;
        }

        $data['kode'] = 'BBM-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $bbm = BBM::create($data);

        // Auto-create record Stok
        Stok::create([
            'bbm_id' => $bbm->id,
            'jumlah' => 0,
            'stok_minimum' => $bbm->stok_minimum ?? 0,
            'stok_maksimum' => $bbm->stok_maksimum ?? 0,
            'lokasi' => $bbm->lokasi ?? '',
        ]);

        AuditLogService::log(
            'create',
            $bbm,
            null,
            null,
            $data,
            'BBM baru dibuat'
        );

        return redirect()
            ->route('bbm.index')
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
        $data = $request->validated();

        $bbm->update($data);

        AuditLogService::log(
            'update',
            $bbm,
            null,
            null,
            $data,
            'BBM diperbarui'
        );

        return redirect()
            ->route('bbm.index')
            ->with('success', 'Jenis BBM berhasil diperbarui.');
    }

public function destroy(BBM $bbm)
{
    try {

        if ($bbm->transaksi()->exists()) {
            return redirect()
                ->route('bbm.index')
                ->with('error', 'BBM tidak dapat dihapus karena sudah digunakan pada transaksi.');
        }

        if ($bbm->po()->exists()) {
            return redirect()
                ->route('bbm.index')
                ->with('error', 'BBM tidak dapat dihapus karena digunakan pada Purchase Order.');
        }

        if ($bbm->stok()->exists()) {
            return redirect()
                ->route('bbm.index')
                ->with('error', 'BBM tidak dapat dihapus karena masih memiliki stok.');
        }

        AuditLogService::log(
            'delete',
            $bbm,
            null,
            null,
            null,
            'BBM dihapus'
        );

        $bbm->delete();

        return redirect()
            ->route('bbm.index')
            ->with('success', 'Jenis BBM berhasil dihapus.');

    } catch (\Throwable $e) {

        return redirect()
            ->route('bbm.index')
            ->with('error', $e->getMessage());
    }
}
}