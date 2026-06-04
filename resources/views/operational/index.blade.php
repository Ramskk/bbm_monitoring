<x-app-layout>
    <x-slot name="header">
        Operasional BBM
    </x-slot>

    <div class="container-fluid">
        <!-- Header with Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-funnel"></i> Filter & Cari
                </h6>
                <a href="{{ route('operational.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Transaksi
                </a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('operational.index') }}" class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label">Kendaraan</label>
                        <select name="kendaraan_id" class="form-select">
                            <option value="">Semua Kendaraan</option>
                            @if(isset($kendaraanList) && $kendaraanList->count())
                                @foreach($kendaraanList->unique('id') as $k)
                                    <option value="{{ $k->id }}" {{ request('kendaraan_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }} ({{ $k->nomor_polisi }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Tanggal Dari</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size: 0.9rem;">No. Transaksi</th>
                                <th style="font-size: 0.9rem;">Kendaraan</th>
                                <th style="font-size: 0.9rem;">BBM</th>
                                <th style="font-size: 0.9rem;">Jumlah</th>
                                <th style="font-size: 0.9rem;">Efisiensi</th>
                                <th style="font-size: 0.9rem;">Tanggal</th>
                                <th style="font-size: 0.9rem;">Status</th>
                                <th style="font-size: 0.9rem;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiList ?? [] as $trans)
                                <tr>
                                    <td><small class="fw-600">{{ $trans->no_transaksi }}</small></td>
                                    <td>
                                        <small class="fw-600">{{ $trans->kendaraan->nama ?? '-' }}</small>
                                        <br>
                                        <small class="text-muted">{{ $trans->kendaraan->nomor_polisi ?? '' }}</small>
                                    </td>
                                    <td><small>{{ $trans->bbm->nama ?? '-' }}</small></td>
                                    <td><small>{{ number_format($trans->jumlah_liter, 2) }} L</small></td>
                                    <td>
                                        <small>
                                            @if($trans->efisiensi)
                                                {{ number_format($trans->efisiensi, 2) }} L/km
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </td>
                                    <td><small>{{ $trans->tanggal_pemakaian?->format('d M Y') ?? '-' }}</small></td>
                                    <td>
                                        @if($trans->status === 'pending')
                                            <span class="badge bg-warning">Menunggu</span>
                                        @elseif($trans->status === 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($trans->status === 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($trans->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('operational.show', $trans->id) }}" class="btn btn-outline-primary" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($trans->status === 'pending' && auth()->user()->hasAnyRole(['admin', 'kadiv']))
                                                <a href="{{ route('operational.edit', $trans->id) }}" class="btn btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('operational.destroy', $trans->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <p class="text-muted mb-0">Belum ada transaksi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($transaksiList) && $transaksiList->hasPages())
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            {{ $transaksiList->links() }}
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
