<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="container-fluid">
        <!-- KPI Widgets Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-2">Total Kendaraan</p>
                            <h3 class="mb-0" style="color: #0066cc;">{{ $totalKendaraan ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: rgba(0, 102, 204, 0.2);">
                            <i class="bi bi-car-front"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top-0">
                        <small class="text-muted">
                            <i class="bi bi-graph-up" style="color: #28a745;"></i>
                            Aktif: {{ $totalKendaraanAktif ?? 0 }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-2">Transaksi Bulan Ini</p>
                            <h3 class="mb-0" style="color: #0066cc;">{{ $transaksiMonth ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: rgba(0, 102, 204, 0.2);">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top-0">
                        <small class="text-muted">
                            <i class="bi bi-fuel-pump"></i>
                            {{ $totalLitersMonth ?? 0 }} L
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-2">Stok Kritis</p>
                            <h3 class="mb-0" style="color: #dc3545;">{{ $stokKritis ? count($stokKritis) : 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: rgba(220, 53, 69, 0.2);">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top-0">
                        <small class="text-danger">
                            <i class="bi bi-alert"></i>
                            Perlu Tindakan
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-2">Pending Approvals</p>
                            <h3 class="mb-0" style="color: #ffc107;">{{ $pendingApprovals ?? 0 }}</h3>
                        </div>
                        <div style="font-size: 2.5rem; color: rgba(255, 193, 7, 0.2);">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top-0">
                        <small class="text-warning">
                            <i class="bi bi-clock"></i>
                            Tertunda
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="mb-0">
                            <i class="bi bi-graph-up"></i> Efisiensi BBM (7 Hari Terakhir)
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="efisiensiChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0">
                        <h6 class="mb-0">
                            <i class="bi bi-pie-chart"></i> Distribusi Stok BBM
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="stokChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-clock-history"></i> Transaksi Terbaru
                        </h6>
                        <a href="{{ route('operational.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size: 0.9rem;">Kendaraan</th>
                                        <th style="font-size: 0.9rem;">BBM</th>
                                        <th style="font-size: 0.9rem;">Jumlah</th>
                                        <th style="font-size: 0.9rem;">Tanggal</th>
                                        <th style="font-size: 0.9rem;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTransactions ?? [] as $trans)
                                        <tr>
                                            <td>
                                                <small class="fw-600">{{ $trans->kendaraan->nama ?? '-' }}</small>
                                                <br>
                                                <small class="text-muted">{{ $trans->kendaraan->nomor_polisi ?? '' }}</small>
                                            </td>
                                            <td><small>{{ $trans->bbm->nama ?? '-' }}</small></td>
                                            <td><small>{{ $trans->jumlah_liter ?? 0 }} L</small></td>
                                            <td><small>{{ $trans->tanggal_pemakaian?->format('d M Y') ?? '-' }}</small></td>
                                            <td>
                                                @if($trans->status === 'pending')
                                                    <span class="badge bg-warning">Menunggu</span>
                                                @elseif($trans->status === 'approved')
                                                    <span class="badge bg-success">Disetujui</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($trans->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <p class="text-muted mb-0">Belum ada transaksi</p>
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
    </div>

    <script>
        // Efisiensi Chart
        @if(isset($efisiensiChartData))
            const efisiensiCtx = document.getElementById('efisiensiChart');
            if (efisiensiCtx) {
                new Chart(efisiensiCtx, {
                    type: 'line',
                    data: {!! json_encode($efisiensiChartData) !!},
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true, position: 'top' },
                            title: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true },
                            x: { ticks: { maxRotation: 45, autoSkip: true } }
                        }
                    }
                });
            }
        @endif

        // Stok Chart
        @if(isset($stokChartData))
            const stokCtx = document.getElementById('stokChart');
            if (stokCtx) {
                new Chart(stokCtx, {
                    type: 'doughnut',
                    data: {!! json_encode($stokChartData) !!},
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true, position: 'bottom' }
                        }
                    }
                });
            }
        @endif
    </script>
</x-app-layout>
