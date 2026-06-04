@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6">Approval</h1>

            <div class="flex gap-2 mb-6 border-b border-gray-200">
                <a href="{{ route('approval.index', ['tab' => 'transaksi']) }}"
                   class="px-4 py-2 -mb-px border-b-2 {{ ($tab ?? 'transaksi') === 'transaksi' ? 'border-blue-600 text-blue-600 font-medium' : 'border-transparent text-gray-500' }}">
                    Transaksi BBM
                </a>
                <a href="{{ route('approval.index', ['tab' => 'po']) }}"
                   class="px-4 py-2 -mb-px border-b-2 {{ ($tab ?? '') === 'po' ? 'border-blue-600 text-blue-600 font-medium' : 'border-transparent text-gray-500' }}">
                    Purchase Order
                </a>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 font-medium">Referensi</th>
                            <th class="text-left px-4 py-3 font-medium">Detail</th>
                            <th class="text-left px-4 py-3 font-medium">Peminta</th>
                            <th class="text-center px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvals as $a)
                            <tr class="border-t border-gray-100 align-top">
                                <td class="px-4 py-3 font-medium">
                                    @if(($tab ?? '') === 'po')
                                        {{ optional($a->approvable)->no_po }}
                                    @else
                                        {{ optional($a->approvable)->no_transaksi }}
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(($tab ?? '') === 'po')
                                        Vendor: {{ optional(optional($a->approvable)->vendor)->nama }}<br>
                                        BBM: {{ optional(optional($a->approvable)->bbm)->nama }}<br>
                                        Jumlah: {{ optional($a->approvable)->jumlah_liter }} L
                                    @else
                                        Kendaraan: {{ optional(optional($a->approvable)->kendaraan)->nomor_polisi }}<br>
                                        BBM: {{ optional(optional($a->approvable)->bbm)->nama }}<br>
                                        Jumlah: {{ optional($a->approvable)->jumlah_liter }} L
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ optional($a->peminta)->name }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('approval.proses') }}" class="flex flex-col gap-2 items-end">
                                        @csrf
                                        <input type="hidden" name="approval_id" value="{{ $a->id }}">
                                        <input type="text" name="catatan" placeholder="Catatan (wajib jika tolak)" class="w-full rounded-md border-gray-300 text-xs">
                                        <div class="flex gap-2">
                                            <button type="submit" name="keputusan" value="approved" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-xs">Setujui</button>
                                            <button type="submit" name="keputusan" value="rejected" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-xs">Tolak</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Tidak ada approval pending</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endcomponent
