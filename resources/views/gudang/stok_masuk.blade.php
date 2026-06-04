@component('layouts.app')
    <div class="bg-white">
        <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6">Stok Masuk</h1>

            <form method="POST" action="{{ route('gudang.stok-masuk') }}">
                @csrf

                <div class="bg-white rounded-lg shadow p-6 border border-gray-200 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis BBM</label>
                        <select name="bbm_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih BBM --</option>
                            @foreach($bbmList as $bbm)
                                <option value="{{ $bbm->id }}" {{ old('bbm_id') == $bbm->id ? 'selected' : '' }}>
                                    {{ $bbm->nama }} ({{ $bbm->kode }})
                                </option>
                            @endforeach
                        </select>
                        @error('bbm_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Liter)</label>
                        <input type="number" step="0.01" name="jumlah" value="{{ old('jumlah') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('jumlah')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga per Liter (opsional)</label>
                        <input type="number" step="0.01" name="harga_per_liter" value="{{ old('harga_per_liter') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('harga_per_liter')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Referensi No (opsional)</label>
                        <input type="text" name="referensi_no" value="{{ old('referensi_no') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <a href="{{ route('gudang.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endcomponent
