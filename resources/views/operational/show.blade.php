<x-app-layout>
    <x-slot name="header">
        Detail Transaksi BBM
    </x-slot>

    <div class="container-fluid">
        <div class="row">
            <!-- Main Content -->
            <div class="col-12 col-lg-8">
                <!-- Info Summary Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid #0066cc;">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-6 col-md-3">
                                <p class="text-muted small mb-2">Kendaraan</p>
                                <p class="fw-600">{{ $transaksi->kendaraan->nama ?? '-' }}</p>
                                <small class="text-muted">{{ $transaksi->kendaraan->nomor_polisi ?? '' }}</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="text-muted small mb-2">BBM</p>
                                <p class="fw-600">{{ $transaksi->bbm->nama ?? '-' }}</p>
                                <small class="text-muted">{{ number_format($transaksi->harga_per_liter, 2) }} Rp/L</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="text-muted small mb-2">Jumlah</p>
                                <p class="fw-600" style="color: #0066cc;">{{ number_format($transaksi->jumlah_liter, 2) }} L</p>
                                <small class="text-muted">Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}</small>
                            </div>
                            <div class="col-6 col-md-3">
                                <p class="text-muted small mb-2">Status</p>
                                @if($transaksi->status === 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($transaksi->status === 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning">Menunggu</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle"></i> Informasi Detail
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">No. Transaksi</label>
                                    <p class="fw-600">{{ $transaksi->no_transaksi }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Pemakaian</label>
                                    <p class="fw-600">{{ $transaksi->tanggal_pemakaian->format('d M Y H:i') }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lokasi Pengisian</label>
                                    <p class="fw-600">{{ $transaksi->lokasi_pengisian ?? '-' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Odometer Sesudah</label>
                                    <p class="fw-600">{{ number_format($transaksi->odometer_sesudah) }} km</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jarak Tempuh</label>
                                    <p class="fw-600">{{ number_format($transaksi->jarak_tempuh, 2) }} km</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Efisiensi Nyata</label>
                                    <p class="fw-600">
                                        @if($transaksi->efisiensi)
                                            {{ number_format($transaksi->efisiensi, 3) }} L/km
                                            @if($transaksi->bbm && $transaksi->bbm->konsumsi_bbm_standar)
                                                <small class="text-muted">(Standar: {{ $transaksi->bbm->konsumsi_bbm_standar }} L/km)</small>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Harga Total</label>
                                    <p class="fw-600" style="color: #0066cc; font-size: 1.25rem;">
                                        Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Dibuat Pada</label>
                                    <p class="fw-600">{{ $transaksi->created_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Timeline -->
                @if($approval)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-0">
                            <h6 class="mb-0">
                                <i class="bi bi-check-circle"></i> Status Approval
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="badge badge-primary" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                        <i class="bi bi-check2"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0 fw-600">
                                        @if($approval->status === 'approved')
                                            <span class="text-success">Disetujui</span>
                                        @elseif($approval->status === 'rejected')
                                            <span class="text-danger">Ditolak</span>
                                        @endif
                                    </p>
                                    <p class="text-muted small mb-0">
                                        Oleh: <strong>{{ $approval->approver->name ?? '-' }}</strong>
                                    </p>
                                    <p class="text-muted small mb-0">
                                        Pada: {{ $approval->processed_at?->format('d M Y H:i') ?? 'Belum diproses' }}
                                    </p>
                                    @if($approval->catatan)
                                        <p class="text-muted small mb-0 mt-2">
                                            <strong>Catatan:</strong> {{ $approval->catatan }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header bg-light border-0">
                        <h6 class="mb-0">
                            <i class="bi bi-sliders"></i> Aksi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($transaksi->status === 'pending' && auth()->user()->hasAnyRole(['admin', 'kadiv']))
                                <a href="{{ route('operational.edit', $transaksi->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Edit Transaksi
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash"></i> Hapus Transaksi
                                </button>
                            @endif
                            <a href="{{ route('operational.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                            </a>
                        </div>

                        <hr class="my-3">

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle"></i>
                            <strong>Status:</strong> 
                            @if($transaksi->status === 'pending')
                                Transaksi menunggu untuk disetujui oleh atasan.
                            @elseif($transaksi->status === 'approved')
                                Transaksi telah disetujui dan tercatat.
                            @elseif($transaksi->status === 'rejected')
                                Transaksi ditolak. Hubungi atasan untuk informasi lebih lanjut.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title">Hapus Transaksi</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('operational.destroy', $transaksi->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
