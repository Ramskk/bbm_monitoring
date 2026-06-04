@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Kendaraan</h3>

                <!-- Info Kendaraan -->
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nomor Polisi</p>
                            <p class="font-bold text-gray-800">{{ $kendaraan->nomor_polisi }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-bold text-gray-800">{{ $kendaraan->nama }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Merek/Model</p>
                            <p class="font-bold text-gray-800">{{ $kendaraan->merek . ' ' . $kendaraan->model }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tahun</p>
                            <p class="font-bold text-gray-800">{{ $kendaraan->tahun }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tabel Detail -->
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium">Atribut</th>
                            <th class="text-left px-4 py-3 font-medium">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Jenis BBM</td><td class="px-4 py-3">{{ $kendaraan->bbm->nama }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Kapasitas Tangki</td><td class="px-4 py-3">{{ $kendaraan->kapasitas_tangki }} L</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Konsumsi BBM Standar</td><td class="px-4 py-3">{{ number_format($kendaraan->bbm->konsumsi_bbm_standar, 2) }} L/km</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Odometer Awal</td><td class="px-4 py-3">{{ $kendaraan->odometer_awal }} km</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Departemen</td><td class="px-4 py-3">{{ $kendaraan->departemen }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Pengemudi Default</td><td class="px-4 py-3">{{ $kendaraan->pengemudi_default }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Status</td><td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $kendaraan->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $kendaraan->status_label }}
                            </span>
                        </td></tr>
                    </tbody>
                </table>

                <!-- Transaksi Terakhir -->
                @if($transaksi->count() > 0)
                    <div class="mt-6">
                        <h4 class="font-bold text-gray-800 mb-3">Riwayat Transaksi (30 Hari Terakhir)</h4>
                        <div class="overflow-auto max-h-64">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="text-left px-3 py-2 font-medium">Tanggal</th>
                                        <th class="text-right px-3 py-2 font-medium">Liters</th>
                                        <th class="text-right px-3 py-2 font-medium">Odometer</th>
                                        <th class="text-right px-3 py-2 font-medium">Efisiensi</th>
                                        <th class="text-center px-3 py-2 font-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transaksi as $trx)
                                        <tr class="border-t border-gray-100">
                                            <td class="px-3 py-2">{{ $trx->tanggal_pemakaian->format('d M Y') }}</td>
                                            <td class="text-right px-3 py-2">{{ number_format($trx->jumlah_liter, 2) }} L</td>
                                            <td class="text-right px-3 py-2">{{ $trx->odometer_sesudah }} km</td>
                                            <td class="text-right px-3 py-2">{{ number_format($trx->efisiensi, 2) }} L/km</td>
                                            <td class="text-center px-3 py-2">
                                                <span class="px-2 py-1 rounded-full text-xs {{ $trx->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                    {{ $trx->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Belum ada transaksi</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endcomponent
