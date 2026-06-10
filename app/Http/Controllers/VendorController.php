<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorRequest;
use App\Models\Vendor;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vendorList = $query->orderBy('nama', 'asc')->paginate(10);

        return view('vendor.index', compact('vendorList'));
    }

    public function create()
    {
        return view('vendor.form');
    }

    public function store(StoreVendorRequest $request)
    {
        Vendor::create($request->validated());

        return redirect()->route('vendor.index')
            ->with('success', 'Vendor berhasil dibuat.');
    }

    public function show(Vendor $vendor)
    {
        return view('vendor.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return view('vendor.form', compact('vendor'));
    }

    public function update(StoreVendorRequest $request, Vendor $vendor)
    {
        $vendor->update($request->validated());

        return redirect()->route('vendor.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor)
    {
        if ($vendor->po()->count() > 0) {
            abort(403, 'Tidak bisa hapus vendor yang memiliki PO.');
        }

        $vendor->delete();

        AuditLogService::log('delete', $vendor, null, null, null, 'Vendor dihapus');

        return redirect()->route('vendor.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }
}