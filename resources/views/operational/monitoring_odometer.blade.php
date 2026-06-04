@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <!-- Monitoring Odometer -->
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Monitoring Odometer Kendaraan Aktif</h3>

                <div class="overflow-auto max-h-96">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="text-left px-3 py-2 font-medium">Nomor Polisi</th>
                                <th class="text-left px-3 py-2 font-medium">Nama Kendaraan</th>
                                <th class="text-center px-3 py-2 font-medium">Odometer Terakhir</th>
                                <th class="text-center px-3 py-2 font-medium">Jenis BBM</th>
                                <th class="text-center px-3 py-2 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $kv)
                                <tr class="border-t border-gray-100 hover:bg-gray-50">
                                    <td class="px-3 py-2 font-medium">{{ $kv->nomor_polisi }}</td>
                                    <td class="px-3 py-2">{{ $kv->nama }}</td>
                                    <td class="text-center px-3 py-2">{{ $kv->odometer_terakhir }} km</td>
                                    <td class="text-center px-3 py-2">{{ $kv->bbm->nama }}</td>
                                    <td class="text-center px-3 py-2">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $kv->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $kv->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Tidak ada kendaraan aktif</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endcomponent
