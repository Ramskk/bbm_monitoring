@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Laporan Biaya Bulan Ini</h3>

                <form method="GET" action="{{ route('laporan.biaya') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun', date('Y')) }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <input type="number" name="tahun" value="{{ request('tahun', date('Y')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="bi bi-search mr-2"></i> Filter
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <canvas id="grafikBiaya" class="w-full" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('grafikBiaya');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($data->pluck('bulan')->toArray()) !!},
                datasets: [{
                    label: 'Biaya per Bulan (Rp)',
                    data: {!! json_encode($data->pluck('biaya')->toArray()) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (v) => 'Rp ' + v.toLocaleString('id-ID') } },
                    x: { ticks: { maxRotation: 45, autoSkip: true } }
                }
            }
        });
    </script>
@endcomponent
