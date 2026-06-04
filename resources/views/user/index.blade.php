@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('user.index') }}">
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select name="role" id="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Role</option>
                                @foreach(app('permission')->roles() as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Aktif</label>
                            <select name="is_active" id="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex items-end">
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
                                <th class="text-left px-4 py-3 font-medium">Nama</th>
                                <th class="text-left px-4 py-3 font-medium">Email</th>
                                <th class="text-left px-4 py-3 font-medium">Role</th>
                                <th class="text-center px-4 py-3 font-medium">Status</th>
                                <th class="text-center px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userList as $u)
                                <tr class="border-t border-gray-100 hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $u->name }}</td>
                                    <td class="px-4 py-3">{{ $u->email }}</td>
                                    <td class="px-4 py-3">{{ $u->roles->pluck('name')->implode(', ') }}</td>
                                    <td class="text-center px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $u->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $u->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td class="text-center px-4 py-3">
                                        <a href="{{ route('user.edit', $u) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                                        <a href="{{ route('user.toggle-status', $u) }}" class="text-green-600 hover:underline" onclick="return confirm('Ubah status akun ini?')">Ubah Status</a>
                                        @if(auth()->check() && auth()->user()->id !== $u->id && auth()->user()->hasAnyRole(['admin', 'super_admin']))
                                            <a href="{{ route('user.destroy', $u) }}" class="text-red-600 hover:underline" onclick="return confirm('Akan menghapus user ini. Apakah Anda yakin?')">Hapus</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada user</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($userList->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-sm text-gray-600">Menampilkan {{ $userList->from() }} - {{ $userList->to() }} dari {{ $userList->total() }} user</span>
                            <div class="flex space-x-1">
                                {{ $userList->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endcomponent
