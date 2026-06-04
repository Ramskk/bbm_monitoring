@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <!-- Form -->
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <form action="{{ route('operational.store') }}" method="POST">
                    @csrf

                    <!-- Kendaraan -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kendaraan</label>
                        <select name="kendaraan_id" id="kendaraan_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih kendaraan</option>
                            @foreach($kendaraanList as $k)
                                <option value="{{ $k->id }}" data-bbm="{{ $k->bbm_id }}" data-kapasitas="{{ $k->kapasitas_tangki }}" data-standar="{{ $k->bbm->konsumsi_bbm_standar }}">
                                    {{ $k->nama }} ({{ $k->nomor_polisi }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="bbm_id" id="bbm_id" value="">
                        <input type="hidden" name="kapasitas_tangki" id="kapasitas_tangki" value="">
                        <input type="hidden" name="standar" id="standar" value="">
                    </div>

                    <!-- Jumlah Liter -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Liter</label>
                        <input type="number" name="jumlah_liter" id="jumlah_liter" step="0.1" min="0.1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Odometer Sesudah -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Odometer Sesudah</label>
                        <input type="number" name="odometer_sesudah" id="odometer_sesudah" step="1" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Tanggal Pemakaian -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pemakaian</label>
                        <input type="date" name="tanggal_pemakaian" id="tanggal_pemakaian" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Lokasi Pengisian -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Pengisian</label>
                        <input type="text" name="lokasi_pengisian" id="lokasi_pengisian" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <!-- Submit -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('operational.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="bi bi-check-square-fill mr-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('kendaraan_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('bbm_id').value = selected.dataset.bbm;
                document.getElementById('kapasitas_tangki').value = selected.dataset.kapasitas;
                document.getElementById('standar').value = selected.dataset.standar;
                document.getElementById('jumlah_liter').max = selected.dataset.kapasitas;
            }
        });
    </script>
@endcomponent
