@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Master Jenis BBM</h1>
                @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'admin_gudang']))
                    <a href="{{ route('bbm.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="bi bi-plus-lg mr-1"></i> Tambah BBM
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('bbm.index') }}">
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                            <input type="text" name="jenis" value="{{ request('jenis') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                <i class="bi bi-search mr-2"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="bg-white rounded-lg shadow border border-gray-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium">Kode</th>
                            <th class="text-left px-4 py-3 font-medium">Nama</th>
                            <th class="text-left px-4 py-3 font-medium">Jenis</th>
                            <th class="text-right px-4 py-3 font-medium">Harga/Liter</th>
                            <th class="text-center px-4 py-3 font-medium">Status</th>
                            <th class="text-center px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bbmList as $bbm)
                            <tr class="border-t border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">{{ $bbm->kode }}</td>
                                <td class="px-4 py-3">{{ $bbm->nama }}</td>
                                <td class="px-4 py-3">{{ $bbm->jenis }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format((float) $bbm->harga_per_liter, 0, ',', '.') }}</td>
                                <td class="text-center px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $bbm->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $bbm->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="text-center px-4 py-3">
                                    @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'admin_gudang']))
                                        <a href="{{ route('bbm.edit', $bbm) }}" class="text-yellow-600 hover:underline">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada data BBM</td></tr>
                        @endforelse
                    </tbody>
                </table>

                @if($bbmList->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $bbmList->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endcomponent
