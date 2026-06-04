<x-app-layout>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="mb-0">
                    <i class="bi bi-file-earmark-pdf me-2" style="color: #0066cc;"></i>
                    Daftar Purchase Order
                </h2>
                <small class="text-muted">Kelola semua Purchase Order BBM</small>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('po.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i> Buat PO Baru
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-2"></i> Filter & Pencarian
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('po.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Vendor</label>
                        <select name="vendor_id" class="form-select">
                            <option value="">Semua Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-2"></i> Filter
                        </button>
                        <a href="{{ route('po.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-clockwise me-2"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- PO Table -->
        <div class="card">
            <div class="card-header bg-light">
                <i class="bi bi-table me-2"></i> Daftar Purchase Order
                <span class="badge bg-info float-end">{{ $pos->total() ?? 0 }} Total</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">
                                <a href="{{ route('po.index', array_merge(request()->query(), ['sort' => 'no_po', 'order' => request('order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                    No. PO
                                    @if(request('sort') == 'no_po')
                                        <i class="bi {{ request('order') == 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('po.index', array_merge(request()->query(), ['sort' => 'vendor_id', 'order' => request('order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                    Vendor
                                    @if(request('sort') == 'vendor_id')
                                        <i class="bi {{ request('order') == 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>BBM</th>
                            <th class="text-end">Quantity (L)</th>
                            <th class="text-end">
                                <a href="{{ route('po.index', array_merge(request()->query(), ['sort' => 'total_nilai', 'order' => request('order') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-dark">
                                    Amount
                                    @if(request('sort') == 'total_nilai')
                                        <i class="bi {{ request('order') == 'asc' ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pos as $po)
                            <tr>
                                <td class="text-center fw-bold">{{ $po->no_po }}</td>
                                <td>{{ $po->vendor->nama ?? '-' }}</td>
                                <td>{{ $po->bbm->nama ?? '-' }}</td>
                                <td class="text-end">{{ number_format($po->jumlah_liter, 2) }}</td>
                                <td class="text-end">Rp {{ number_format($po->total_nilai, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @switch($po->status)
                                        @case('draft')
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-hourglass-split me-1"></i> Draft
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Approved
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i> Ditolak
                                            </span>
                                            @break
                                        @case('dikirim')
                                            <span class="badge bg-info">
                                                <i class="bi bi-truck me-1"></i> Dikirim
                                            </span>
                                            @break
                                        @case('diterima')
                                            <span class="badge bg-primary">
                                                <i class="bi bi-box-seam me-1"></i> Diterima
                                            </span>
                                            @break
                                        @case('closed')
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-check2-square me-1"></i> Closed
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $po->status }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('po.show', $po) }}" class="btn btn-outline-primary" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Tidak ada data Purchase Order</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-md-6">
                @if(isset($pos) && $pos->total() > 0)
                    <small class="text-muted">
                        Menampilkan {{ $pos->firstItem() }} hingga {{ $pos->lastItem() }} dari {{ $pos->total() }} data
                    </small>
                @endif
            </div>
            <div class="col-md-6">
                @if(isset($pos))
                    <nav aria-label="Page navigation" class="d-flex justify-content-end">
                        {{ $pos->links('pagination::bootstrap-5') }}
                    </nav>
                @endif
            </div>
        </div>
    </div>


</x-app-layout>
