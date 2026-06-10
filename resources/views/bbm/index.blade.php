@component('layouts.app')

<div class="bg-white">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                Master Jenis BBM
            </h1>

            @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'admin_gudang']))
                <a href="{{ route('bbm.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Tambah BBM
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700 border border-red-300">
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('bbm.index') }}">
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis
                        </label>

                        <input
                            type="text"
                            name="jenis"
                            value="{{ request('jenis') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status
                        </label>

                        <select
                            name="is_active"
                            class="w-full rounded-md border-gray-300 shadow-sm">

                            <option value="">Semua</option>

                            <option value="1"
                                {{ request('is_active') === '1' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="0"
                                {{ request('is_active') === '0' ? 'selected' : '' }}>
                                Tidak Aktif
                            </option>

                        </select>
                    </div>

                    <div class="flex items-end">
                        <button
                            type="submit"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Cari
                        </button>
                    </div>

                </div>

            </div>
        </form>

        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-50">

                <tr>
                    <th class="text-left px-4 py-3">Kode</th>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Jenis</th>
                    <th class="text-right px-4 py-3">Harga/Liter</th>
                    <th class="text-center px-4 py-3">Status</th>
                    <th class="text-center px-4 py-3">Aksi</th>
                </tr>

                </thead>

                <tbody>

                @forelse($bbmList as $bbm)

                    <tr class="border-t hover:bg-gray-50">

                        <td class="px-4 py-3">
                            {{ $bbm->kode }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $bbm->nama }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $bbm->jenis }}
                        </td>

                        <td class="px-4 py-3 text-right">
                            Rp {{ number_format($bbm->harga_per_liter, 0, ',', '.') }}
                        </td>

                        <td class="text-center px-4 py-3">

                            @if($bbm->is_active)
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs">
                                    Tidak Aktif
                                </span>
                            @endif

                        </td>

                        <td class="text-center px-4 py-3">

                            @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'admin_gudang']))

                                <div class="flex justify-center gap-4">

                                    <a href="{{ route('bbm.edit', $bbm) }}"
                                       class="text-yellow-600 hover:text-yellow-800">
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('bbm.destroy', $bbm) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data BBM ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-red-600 hover:text-red-800">
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6"
                            class="text-center py-10 text-gray-500">
                            Belum ada data BBM
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

            @if($bbmList->hasPages())

                <div class="p-4 border-t">
                    {{ $bbmList->withQueryString()->links() }}
                </div>

            @endif

        </div>

    </div>
</div>

@endcomponent