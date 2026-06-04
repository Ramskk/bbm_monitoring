@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Purchase Order</h3>

                <!-- Info Vendor & BBM -->
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Vendor</p>
                            <p class="font-bold text-gray-800">{{ $po->vendor->nama }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">BBM</p>
                            <p class="font-bold text-gray-800">{{ $po->bbm->nama }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Jumlah Liter</p>
                            <p class="font-bold text-gray-800">{{ number_format($po->jumlah_liter, 2) }} L</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Nilai</p>
                            <p class="font-bold text-gray-800">{{ number_format($po->total_nilai, 2) }} Rp</p>
                        </div>
                    </div>
                </div>

                <!-- Tabel Detail -->
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium">No. PO</th>
                            <th class="text-left px-4 py-3 font-medium">Vendor</th>
                            <th class="text-left px-4 py-3 font-medium">BBM</th>
                            <th class="text-right px-4 py-3 font-medium">Jumlah</th>
                            <th class="text-right px-4 py-3 font-medium">Harga/Liter</th>
                            <th class="text-right px-4 py-3 font-medium">Total Nilai</th>
                            <th class="text-left px-4 py-3 font-medium">Tanggal PO</th>
                            <th class="text-left px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3 font-medium">{{ $po->no_po }}</td>
                            <td class="px-4 py-3">{{ $po->vendor->nama }}</td>
                            <td class="px-4 py-3">{{ $po->bbm->nama }}</td>
                            <td class="text-right px-4 py-3">{{ number_format($po->jumlah_liter, 2) }} L</td>
                            <td class="text-right px-4 py-3">{{ number_format($po->harga_per_liter, 2) }} Rp</td>
                            <td class="text-right px-4 py-3">{{ number_format($po->total_nilai, 2) }} Rp</td>
                            <td class="px-4 py-3">{{ $po->tanggal_po->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs {{ $po->status_badge }}">
                                    {{ $po->status_badge }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Timeline Status -->
                <div class="mt-6">
                    <h4 class="font-bold text-gray-800 mb-3">Riwayat Status</h4>
                    <div class="space-y-3">
                        <div class="relative pl-6">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            <div class="absolute left-[-4px] top-1.5 w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="font-bold text-gray-800">Draft</p>
                                <p class="text-sm text-gray-600">{{ $po->tanggal_po->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="relative pl-6">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            <div class="absolute left-[-4px] top-1.5 w-2.5 h-2.5 rounded-full {{ $po->status === 'approved' ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="font-bold text-gray-800">Approved</p>
                                <p class="text-sm text-gray-600">{{ $po->approved_at->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="relative pl-6">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            <div class="absolute left-[-4px] top-1.5 w-2.5 h-2.5 rounded-full {{ $po->status === 'dikirim' ? 'bg-blue-500' : 'bg-gray-200' }}"></div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="font-bold text-gray-800">Dikirim</p>
                                <p class="text-sm text-gray-600">{{ $po->tanggal_kirim_aktual->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="relative pl-6">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            <div class="absolute left-[-4px] top-1.5 w-2.5 h-2.5 rounded-full {{ $po->status === 'diterima' ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="font-bold text-gray-800">Diterima</p>
                                <p class="text-sm text-gray-600">{{ $po->tanggal_terima->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="relative pl-6">
                            <div class="absolute left-0 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            <div class="absolute left-[-4px] top-1.5 w-2.5 h-2.5 rounded-full {{ $po->status === 'closed' ? 'bg-gray-500' : 'bg-gray-200' }}"></div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <p class="font-bold text-gray-800">Closed</p>
                                <p class="text-sm text-gray-600">{{ $po->tanggal_terima->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('po.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        <i class="bi bi-arrow-left mr-2"></i> Kembali
                    </a>
                    @if($po->status === 'draft')
                        <a href="{{ route('po.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="bi bi-plus-circle mr-2"></i> Buat PO Baru
                        </a>
                    @endif
                    @if($po->status === 'approved' && $po->tanggal_kirim_rencana < now() || $po->tanggal_kirim_aktual < now())
                        <a href="{{ route('po.close', $po) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            <i class="bi bi-check-circle mr-2"></i> Tutup PO
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endcomponent
