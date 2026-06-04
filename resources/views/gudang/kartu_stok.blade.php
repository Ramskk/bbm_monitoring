<x-app-layout>
    <div class="py-5">
        <div class="container-fluid px-4">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('gudang.index') }}" style="color: #0066cc; text-decoration: none;">
                            <i class="bi bi-house me-1"></i>Gudang
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Kartu Stok</li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0" style="color: #0066cc;">
                        <i class="bi bi-receipt me-2"></i>Kartu Stok
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
                                    <p class="text-muted small mb-2">Total Masuk</p>
                                    <p class="h4 mb-0 fw-bold text-success">
                                        @php
                                            $totalMasuk = $mutasi->where('jenis_badge', 'masuk')->sum('nilai_mutasi');
                                        @endphp
                                        {{ number_format($totalMasuk, 2) }} L
                                    </p>
                                </div>
                                <i class="bi bi-arrow-down text-success fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">Total Keluar</p>
                                    <p class="h4 mb-0 fw-bold text-danger">
                                        @php
                                            $totalKeluar = $mutasi->where('jenis_badge', 'keluar')->sum('nilai_mutasi');
                                        @endphp
                                        {{ number_format($totalKeluar, 2) }} L
                                    </p>
                                </div>
                                <i class="bi bi-arrow-up text-danger fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small mb-2">Stok Saat Ini</p>
                                    <p class="h4 mb-0 fw-bold" style="color: #0066cc;">
                                        @php
                                            $currentStock = $mutasi->last()?->stok_sesudah ?? 0;
                                        @endphp
                                        {{ number_format($currentStock, 2) }} L
                                    </p>
                                </div>
                                <i class="bi bi-fuel-pump fs-5" style="color: #0066cc;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form id="kartuStokForm" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Dari Tanggal</label>
                            <input type="date" name="dari" id="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Sampai Tanggal</label>
                            <input type="date" name="sampai" id="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Jenis Transaksi</label>
                            <select name="jenis" id="jenis" class="form-select form-select-sm">
                                <option value="">Semua Jenis</option>
                                <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                                <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100" style="background-color: #0066cc; border-color: #0066cc;">
                                <i class="bi bi-search me-1"></i>Cari
                            </button>
                            <a href="{{ route('gudang.kartu-stok', request('stok_id')) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transaction History Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-left-right me-2"></i>Riwayat Transaksi
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bold">Tanggal</th>
                                    <th class="fw-bold">Jenis</th>
                                    <th class="fw-bold">BBM</th>
                                    <th class="fw-bold">Referensi</th>
                                    <th class="text-end fw-bold">Stok Sebelum</th>
                                    <th class="text-end fw-bold">Nilai Mutasi</th>
                                    <th class="text-end fw-bold">Stok Sesudah</th>
                                    <th class="text-end fw-bold">Harga/Liter</th>
                                    <th class="text-center fw-bold">Tipe</th>
                                </tr>
                            </thead>
                            <tbody id="kartuStokTable">
                                @forelse($mutasi as $m)
                                    <tr>
                                        <td>{{ $m->tanggal->format('d M Y H:i') }}</td>
                                        <td>{{ $m->jenis_label }}</td>
                                        <td>{{ $m->bbm->nama }}</td>
                                        <td>{{ $m->referensi_no }}</td>
                                        <td class="text-end">{{ number_format($m->stok_sebelum ?? 0, 2) }} L</td>
                                        <td class="text-end">{{ number_format($m->nilai_mutasi, 2) }} L</td>
                                        <td class="text-end">{{ number_format($m->stok_sesudah ?? 0, 2) }} L</td>
                                        <td class="text-end">{{ number_format($m->harga_per_liter ?? 0, 2) }} Rp</td>
                                        <td class="text-center">
                                            @if($m->jenis_badge === 'masuk')
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
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox me-2"></i>Belum ada data
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
</x-app-layout>
