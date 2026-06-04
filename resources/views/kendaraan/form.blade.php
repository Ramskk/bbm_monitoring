@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <form action="{{ route('kendaraan.store') }}" method="POST">
                @csrf

                <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis BBM</label>
                        <select name="bbm_id" id="bbm_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih jenis BBM</option>
                            @foreach($bbmList as $bbm)
                                <option value="{{ $bbm->id }}">
                                    {{ $bbm->nama }} (Stok: {{ number_format($bbm->stok->jumlah ?? 0, 2) }} L)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Polisi</label>
                        <input type="text" name="nomor_polisi" id="nomor_polisi" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kendaraan</label>
                        <input type="text" name="nama" id="nama" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Merek</label>
                        <input type="text" name="merek" id="merek" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <input type="text" name="model" id="model" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <input type="number" name="tahun" id="tahun" min="2000" max="2030" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis BBM</label>
                        <select name="jenis" id="jenis" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Solar">Solar</option>
                            <option value="Pertalite">Pertalite</option>
                            <option value="Pertamax">Pertamax</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Tangki (Liter)</label>
                        <input type="number" name="kapasitas_tangki" id="kapasitas_tangki" step="1" min="100" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konsumsi BBM Standar (L/km)</label>
                        <input type="number" name="konsumsi_bbm_standar" id="konsumsi_bbm_standar" step="0.1" min="0.1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Odometer Awal (km)</label>
                        <input type="number" name="odometer_awal" id="odometer_awal" step="1" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                        <input type="text" name="departemen" id="departemen" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengemudi Default</label>
                        <input type="text" name="pengemudi_default" id="pengemudi_default" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('kendaraan.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endcomponent
