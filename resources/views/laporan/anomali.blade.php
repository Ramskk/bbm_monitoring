@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Deteksi Anomali Kendaraan Boros</h3>

                <form method="GET" action="{{ route('laporan.anomali') }}">
                    <input type="hidden" name="dari" value="{{ $dari }}">
                    <input type="hidden" name="sampai" value="{{ $sampai }}">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                        <input type="date" name="dari" value="{{ request('dari', now()->subDays(30)) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sampai</label>
                        <input type="date" name="sampai" value="{{ request('sampai', now()) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="bi bi-search mr-2"></i> Cari
                        </button>
                    </div>
                </form>

                <div class="mt-6 overflow-auto max-h-96">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium">Nomor Polisi</th>
                                <th class="text-left px-3 py-2 font-medium">Nama Kendaraan</th>
                                <th class="text-left px-3 py-2 font-medium">Jenis BBM</th>
                                <th class="text-left px-3 py-2 font-medium">Efisiensi</th>
                                <th class="text-left px-3 py-2 font-medium">Standar</th>
                                <th class="text-right px-3 py-2 font-medium">Deviasi</th>
                                <th class="text-center px-3 py-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $item)
                                <tr class="border-t border-gray-100">
                                    <td class="px-3 py-2 font-medium">{{ $item->nomor_polisi }}</td>
                                    <td class="px-3 py-2">{{ $item->nama }}</td>
                                    <td class="px-3 py-2">{{ $item->bbm->nama }}</td>
                                    <td class="px-3 py-2">{{ number_format($item->efisiensi, 2) }} L/km</td>
                                    <td class="px-3 py-2">{{ number_format($item->standar, 2) }} L/km</td>
                                    <td class="text-right px-3 py-2">
                                        <span class="text-red-600 font-bold">{{ number_format($item->deviasi_efisiensi, 2) }}%</span>
                                    </td>
                                    <td class="text-center px-3 py-2">
                                        <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Boros</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-3 py-4 text-center text-gray-400">Tidak ada anomali ditemukan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent
