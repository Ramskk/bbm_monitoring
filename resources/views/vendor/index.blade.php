@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

            {{-- HEADER + BUTTON TAMBAH --}}
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Data Vendor</h2>

                @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'admin_pengadaan']))
                    <a href="{{ route('vendor.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        + Tambah Vendor
                    </a>
                @endif
            </div>

            {{-- FILTER --}}
            <form method="GET" action="{{ route('vendor.index') }}">
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="aktif">Aktif</option>
                                <option value="non_aktif">Non Aktif</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium">Kode Vendor</th>
                                <th class="text-left px-4 py-3 font-medium">Nama Vendor</th>
                                <th class="text-left px-4 py-3 font-medium">Alamat</th>
                                <th class="text-left px-4 py-3 font-medium">Kota</th>
                                <th class="text-left px-4 py-3 font-medium">Telepon</th>
                                <th class="text-left px-4 py-3 font-medium">Email</th>
                                <th class="text-center px-4 py-3 font-medium">Status</th>
                                <th class="text-center px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($vendorList as $v)
                                <tr class="border-t border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">{{ $v->kode }}</td>
                                    <td class="px-4 py-3">{{ $v->nama }}</td>
                                    <td class="px-4 py-3">{{ $v->alamat }}</td>
                                    <td class="px-4 py-3">{{ $v->kota }}</td>
                                    <td class="px-4 py-3">{{ $v->telepon }}</td>
                                    <td class="px-4 py-3">{{ $v->email }}</td>

                                    <td class="text-center px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs
                                            {{ $v->status === 'aktif'
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-red-100 text-red-700' }}">
                                            {{ $v->status_badge }}
                                        </span>
                                    </td>

                                    <td class="text-center px-4 py-3">
                                        <a href="{{ route('vendor.show', $v) }}"
                                           class="text-blue-600 hover:underline mr-2">
                                            Detail
                                        </a>

                                        @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'admin_pengadaan']))
                                            <a href="{{ route('vendor.edit', $v) }}"
                                               class="text-yellow-600 hover:underline mr-2">
                                                Edit
                                            </a>
                                        @endif

                                        @if(auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin', 'admin_pengadaan']) && $v->po->count() === 0)
                                            <a href="{{ route('vendor.destroy', $v) }}"
                                               class="text-red-600 hover:underline"
                                               onclick="return confirm('Akan menghapus vendor ini. Apakah Anda yakin?')">
                                                Hapus
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                        Belum ada vendor
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- PAGINATION --}}
                    @if($vendorList->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-sm text-gray-600">
                                Menampilkan {{ $vendorList->from() }} - {{ $vendorList->to() }}
                                dari {{ $vendorList->total() }} vendor
                            </span>

                            <div class="flex space-x-1">
                                {{ $vendorList->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </form>

        </div>
    </div>
@endcomponent