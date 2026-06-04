@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Vendor</h3>

                <!-- Info Vendor -->
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium">Atribut</th>
                            <th class="text-left px-4 py-3 font-medium">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Kode Vendor</td><td class="px-4 py-3 font-medium">{{ $vendor->kode }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Nama</td><td class="px-4 py-3 font-medium">{{ $vendor->nama }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Alamat</td><td class="px-4 py-3">{{ $vendor->alamat }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Kota</td><td class="px-4 py-3">{{ $vendor->kota }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Telepon</td><td class="px-4 py-3">{{ $vendor->telepon }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Email</td><td class="px-4 py-3">{{ $vendor->email }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">NPWP</td><td class="px-4 py-3">{{ $vendor->npwp }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Rekening Bank</td><td class="px-4 py-3">{{ $vendor->rekening_bank }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Nama Bank</td><td class="px-4 py-3">{{ $vendor->nama_bank }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Atas Nama</td><td class="px-4 py-3">{{ $vendor->atas_nama }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Kontak Person</td><td class="px-4 py-3">{{ $vendor->kontak_person }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Kontak Telepon</td><td class="px-4 py-3">{{ $vendor->kontak_telepon }}</td></tr>
                        <tr class="border-t border-gray-100"><td class="px-4 py-3">Status</td><td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $vendor->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $vendor->status_badge }}
                            </span>
                        </td></tr>
                    </tbody>
                </table>

                <!-- PO yang terkait -->
                @if($vendor->po->count() > 0)
                    <div class="mt-6">
                        <h4 class="font-bold text-gray-800 mb-3">Purchase Order</h4>
                        <div class="space-y-2">
                            @foreach($vendor->po as $po)
                                <div class="flex justify-between items-center bg-gray-50 rounded-lg p-3 border border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $po->no_po }}</p>
                                        <p class="text-sm text-gray-600">{{ $po->tanggal_po->format('d M Y') }}</p>
                                    </div>
                                    <span class="px-2 py-1 rounded-full text-xs {{ $po->status_badge }}">
                                        {{ $po->status_badge }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endcomponent
