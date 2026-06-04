@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail User</h3>

                <!-- Info User -->
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium">Atribut</th>
                            <th class="text-left px-4 py-3 font-medium">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Nama</td><td class="px-4 py-3 font-medium">{{ $user->name }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Email</td><td class="px-4 py-3 font-medium">{{ $user->email }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Role</td><td class="px-4 py-3">{{ $user->roles->pluck('name')->implode(', ') }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Departemen</td><td class="px-4 py-3">{{ $user->departemen }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Jabatan</td><td class="px-4 py-3">{{ $user->jabatan }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Telepon</td><td class="px-4 py-3">{{ $user->telepon }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Status</td><td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
