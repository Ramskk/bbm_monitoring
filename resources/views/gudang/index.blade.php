<x-app-layout>
    <div class="py-5">
        <div class="container-fluid px-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0" style="color: #0066cc;">
                        <i class="bi bi-fuel-pump me-2"></i>Manajemen Stok Gudang
                    </h1>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">Total Stok</p>
                                    <p class="h4 mb-0 fw-bold">{{ count($stokList) }} jenis</p>
                                </div>
                                <i class="bi bi-box2 text-primary fs-5" style="color: #0066cc !important;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">Stok Kritis</p>
                                    <p class="h4 mb-0 fw-bold text-danger">{{ count($stokKritis) }}</p>
                                </div>
                                <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">Stok Penuh</p>
                                    <p class="h4 mb-0 fw-bold text-success">{{ count($stokPenuh) }}</p>
                                </div>
                                <i class="bi bi-check-circle text-success fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Cari Jenis BBM</label>
                            <input type="text" class="form-control form-control-sm" id="searchBBM" placeholder="Ketik nama BBM...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Filter Status</label>
                            <select class="form-select form-select-sm" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="kritis">Kritis</option>
                                <option value="normal">Normal</option>
                                <option value="penuh">Penuh</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Table -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Data Stok BBM</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bold">Jenis BBM</th>
                                    <th class="text-end fw-bold">Stok Saat Ini</th>
                                    <th class="text-end fw-bold">Min</th>
                                    <th class="text-end fw-bold">Maks</th>
                                    <th class="text-center fw-bold">Persentase</th>
                                    <th class="text-center fw-bold">Status</th>
                                    <th class="text-center fw-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokList as $stok)
                                    <tr>
                                        <td>{{ $stok->bbm->nama }}</td>
                                        <td class="text-end">{{ number_format($stok->jumlah, 2) }} L</td>
                                        <td class="text-end">{{ $stok->stok_minimum }} L</td>
                                        <td class="text-end">{{ $stok->stok_maksimum }} L</td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2 flex-grow-1" style="height: 20px; background-color: #f0f0f0;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $stok->persentase }}%; background-color: {{ $stok->isKritis() ? '#dc3545' : ($stok->isPenuh() ? '#198754' : '#0066cc') }};"
                                                         aria-valuenow="{{ $stok->persentase }}" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="fw-bold">{{ $stok->persentase }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($stok->isKritis())
                                                <span class="badge bg-danger">Kritis</span>
                                            @elseif($stok->isPenuh())
                                                <span class="badge bg-success">Penuh</span>
                                            @else
                                                <span class="badge bg-primary" style="background-color: #0066cc !important;">Normal</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('gudang.kartu-stok', $stok->id) }}" class="btn btn-sm btn-outline-primary" style="--bs-btn-color: #0066cc; --bs-btn-border-color: #0066cc;">
                                                <i class="bi bi-receipt me-1"></i>Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox me-2"></i>Belum ada data stok
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Mutations -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-left-right me-2"></i>Mutasi Stok Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="fw-bold">Tanggal</th>
                                    <th class="fw-bold">Jenis</th>
                                    <th class="fw-bold">BBM</th>
                                    <th class="text-end fw-bold">Nilai Mutasi</th>
                                    <th class="text-center fw-bold">Tipe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mutasiTerbaru as $mutasi)
                                    <tr>
                                        <td>{{ $mutasi->tanggal->format('d M Y H:i') }}</td>
                                        <td>{{ $mutasi->jenis_label }}</td>
                                        <td>{{ $mutasi->bbm->nama }}</td>
                                        <td class="text-end">{{ number_format($mutasi->nilai_mutasi, 2) }} L</td>
                                        <td class="text-center">
                                            @if($mutasi->jenis_badge === 'masuk')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-arrow-down me-1"></i>Masuk
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-arrow-up me-1"></i>Keluar
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox me-2"></i>Belum ada mutasi
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Filter functionality
        document.getElementById('filterStatus')?.addEventListener('change', function() {
            const status = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const badge = row.querySelector('.badge');
                if (!status || badge?.textContent.toLowerCase().includes(status)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('searchBBM')?.addEventListener('keyup', function() {
            const search = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const bbmName = row.cells[0]?.textContent.toLowerCase();
                if (!search || bbmName?.includes(search)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.querySelector('.btn-outline-secondary')?.addEventListener('click', function() {
            document.getElementById('searchBBM').value = '';
            document.getElementById('filterStatus').value = '';
            document.querySelectorAll('table tbody tr').forEach(row => {
                row.style.display = '';
            });
        });
    </script>
    @endpush
</x-app-layout>
