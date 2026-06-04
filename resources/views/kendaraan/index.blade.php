@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('kendaraan.index') }}">
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Non_Aktif">Non Aktif</option>
                                <option value="Dijual">Dijual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                            <select name="departemen" id="departemen" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua</option>
                                @if($kendaraanList->count())
                                    @foreach(array_unique($kendaraanList->pluck('departemen')->toArray()) as $d)
                                        <option value="{{ $d }}">{{ $d }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                <i class="bi bi-search mr-2"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium">Nomor Polisi</th>
                                <th class="text-left px-4 py-3 font-medium">Nama Kendaraan</th>
                                <th class="text-left px-4 py-3 font-medium">Merek/Model</th>
                                <th class="text-left px-4 py-3 font-medium">Tahun</th>
                                <th class="text-left px-4 py-3 font-medium">Jenis BBM</th>
                                <th class="text-left px-4 py-3 font-medium">Kapasitas</th>
                                <th class="text-center px-4 py-3 font-medium">Status</th>
                                <th class="text-center px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kendaraanList as $kv)
                                <tr class="border-t border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $kv->nomor_polisi }}</td>
                                    <td class="px-4 py-3">{{ $kv->nama }}</td>
                                    <td class="px-4 py-3">{{ $kv->merek . ' ' . $kv->model }}</td>
                                    <td class="px-4 py-3">{{ $kv->tahun }}</td>
                                    <td class="px-4 py-3">{{ $kv->bbm->nama }}</td>
                                    <td class="px-4 py-3">{{ $kv->kapasitas_tangki }} L</td>
                                    <td class="text-center px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $kv->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $kv->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-center px-4 py-3">
                                        <a href="{{ route('kendaraan.show', $kv) }}" class="text-blue-600 hover:underline mr-2">Detail</a>
                                        @if($kv->is_active && auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                            <a href="{{ route('kendaraan.edit', $kv) }}" class="text-yellow-600 hover:underline mr-2">Edit</a>
                                        @endif
                                        @if($kv->is_active && auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin']) && $kv->transaksi->count() === 0)
                                            <a href="{{ route('kendaraan.create') }}" class="text-red-600 hover:underline" onclick="return confirm('Akan menghapus kendaraan ini. Apakah Anda yakin?')">Hapus</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">Belum ada kendaraan</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($kendaraanList->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-sm text-gray-600">Menampilkan {{ $kendaraanList->from() }} - {{ $kendaraanList->to() }} dari {{ $kendaraanList->total() }} kendaraan</span>
                            <div class="flex space-x-1">
                                {{ $kendaraanList->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endcomponent
