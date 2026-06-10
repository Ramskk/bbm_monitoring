@component('layouts.app')
<div class="bg-white">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <form action="{{ isset($vendor) ? route('vendor.update', $vendor) : route('vendor.store') }}"
              method="POST">

            @csrf
            @if(isset($vendor))
                @method('PUT')
            @endif

            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <input type="text" name="kode" placeholder="Kode Vendor"
                           value="{{ $vendor->kode ?? '' }}"
                           class="w-full border rounded-md">

                    <input type="text" name="nama" placeholder="Nama"
                           value="{{ $vendor->nama ?? '' }}"
                           class="w-full border rounded-md">

                    <input type="text" name="alamat" placeholder="Alamat"
                           value="{{ $vendor->alamat ?? '' }}"
                           class="w-full border rounded-md">

                    <input type="text" name="kota" placeholder="Kota"
                           value="{{ $vendor->kota ?? '' }}"
                           class="w-full border rounded-md">
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <input type="text" name="telepon" placeholder="Telepon"
                           value="{{ $vendor->telepon ?? '' }}"
                           class="w-full border rounded-md">

                    <input type="email" name="email" placeholder="Email"
                           value="{{ $vendor->email ?? '' }}"
                           class="w-full border rounded-md">

                    <input type="text" name="npwp" placeholder="NPWP"
                           value="{{ $vendor->npwp ?? '' }}"
                           class="w-full border rounded-md">

                    <input type="text" name="rekening_bank" placeholder="Rekening"
                           value="{{ $vendor->rekening_bank ?? '' }}"
                           class="w-full border rounded-md">
                </div>

                <div class="mb-4">
                    <label>Status</label>
                    <select name="status" class="w-full border rounded-md">

                        <option value="Aktif"
                            {{ isset($vendor) && $vendor->status === 'Aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="Non_Aktif"
                            {{ isset($vendor) && $vendor->status === 'Non_Aktif' ? 'selected' : '' }}>
                            Non Aktif
                        </option>

                    </select>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('vendor.index') }}"
                       class="bg-gray-500 text-white px-4 py-2 rounded-lg">
                        Batal
                    </a>

                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Simpan
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>
@endcomponent