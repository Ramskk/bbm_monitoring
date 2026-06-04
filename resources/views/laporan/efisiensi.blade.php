@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Laporan Efisiensi BBM</h3>

                <form method="GET" action="{{ route('laporan.efisiensi') }}">
                    <input type="hidden" name="departemen" value="{{ $departemen }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                        <select name="departemen" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Departemen</option>
                            @foreach($data->pluck('departemen')->filter()->unique() as $d)
                                <option value="{{ $d }}" {{ request('departemen') === $d ? 'selected' : '' }}>
                                    {{ $d }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="bi bi-search mr-2"></i> Filter
                        </button>
                    </div>
                </form>

                <div class="mt-6 overflow-auto max-h-96">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium">Kendaraan</th>
                                <th class="text-left px-3 py-2 font-medium">Jenis BBM</th>
                                <th class="text-left px-3 py-2 font-medium">Vendor</th>
                                <th class="text-left px-3 py-2 font-medium">Efisiensi</th>
                                <th class="text-right px-3 py-2 font-medium">Standar</th>
                                <th class="text-center px-3 py-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-2">
                                        <span class="font-medium">{{ $item->nama }}</span>
                                        <div class="text-xs text-gray-500">{{ $item->merek . ' ' . $item->model }}</div>
                                    </td>
                                    <td class="px-3 py-2">{{ $item->bbm->nama }}</td>
                                    <td class="px-3 py-2">{{ $item->vendor ? $item->vendor->nama : '-' }}</td>
                                    <td class="px-3 py-2">{{ number_format($item->efisiensi, 2) }} L/km</td>
                                    <td class="text-right px-3 py-2">{{ number_format($item->standar, 2) }} L/km</td>
                                    <td class="text-center px-3 py-2">
                                        @if($item->efisiensi >= $item->standar * 1.2)
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Sangat Efisien</span>
                                        @elseif($item->efisiensi >= $item->standar * 0.8)
                                            <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Normal</span>
                                        @elseif($item->efisiensi >= $item->standar * 0.6)
                                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Perlu Perhatian</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Boros</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-4 text-center text-gray-400">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <canvas id="grafikEfisiensi" class="w-full" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('grafikEfisiensi');
        new Chart(ctx, {
            type: 'bar',
            data: {!! json_encode($grafikData) !!},
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 10 } },
                    x: { ticks: { maxRotation: 45, autoSkip: true } }
                }
            }
        });
    </script>
@endcomponent
