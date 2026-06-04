<x-app-layout>
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="mb-0">
                    <i class="bi bi-plus-circle me-2" style="color: #0066cc;"></i>
                    Buat Purchase Order Baru
                </h2>
                <small class="text-muted">Lengkapi form untuk membuat PO baru</small>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-file-earmark-text me-2"></i> Informasi Purchase Order
                    </div>
                    <div class="card-body">
                        <form action="{{ route('po.store') }}" method="POST" id="poForm">
                            @csrf

                            <!-- Vendor Selection -->
                            <div class="mb-3">
                                <label for="vendor_id" class="form-label">
                                    <i class="bi bi-shop me-1" style="color: #0066cc;"></i> Vendor
                                </label>
                                <select name="vendor_id" id="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Vendor --</option>
                                    @foreach($vendorList as $vendor)
                                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendor_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- BBM Selection -->
                            <div class="mb-3">
                                <label for="bbm_id" class="form-label">
                                    <i class="bi bi-fuel-pump me-1" style="color: #0066cc;"></i> Jenis BBM
                                </label>
                                <select name="bbm_id" id="bbm_id" class="form-select @error('bbm_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis BBM --</option>
                                    @foreach($bbmList as $bbm)
                                        <option value="{{ $bbm->id }}" {{ old('bbm_id') == $bbm->id ? 'selected' : '' }}>
                                            {{ $bbm->nama }} (Stok: {{ number_format($bbm->stok->jumlah ?? 0, 2) }} L)
                                        </option>
                                    @endforeach
                                </select>
                                @error('bbm_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- Quantity Section -->
                            <h5 class="mb-3">
                                <i class="bi bi-basket3 me-2" style="color: #0066cc;"></i> Detail Pemesanan
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jumlah_liter" class="form-label">
                                            <i class="bi bi-droplet me-1" style="color: #0066cc;"></i> Jumlah Liter
                                        </label>
                                        <input type="number" name="jumlah_liter" id="jumlah_liter" 
                                               step="0.1" min="0.1" class="form-control @error('jumlah_liter') is-invalid @enderror"
                                               value="{{ old('jumlah_liter') }}" required>
                                        @error('jumlah_liter')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted d-block mt-1">Minimal 0.1 liter</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="harga_per_liter" class="form-label">
                                            <i class="bi bi-cash me-1" style="color: #0066cc;"></i> Harga per Liter (Rp)
                                        </label>
                                        <input type="number" name="harga_per_liter" id="harga_per_liter" 
                                               step="0.01" min="0" class="form-control @error('harga_per_liter') is-invalid @enderror"
                                               value="{{ old('harga_per_liter') }}" required
                                               placeholder="0">
                                        @error('harga_per_liter')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Total Calculation -->
                            <div class="mb-3">
                                <label for="total_nilai" class="form-label">
                                    <i class="bi bi-calculator me-1" style="color: #0066cc;"></i> Total Nilai (Rp)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="total_nilai" id="total_nilai" 
                                           class="form-control bg-light" readonly>
                                </div>
                                <small class="text-muted d-block mt-1">Otomatis dihitung dari Jumlah × Harga per Liter</small>
                            </div>

                            <hr class="my-4">

                            <!-- Delivery Section -->
                            <h5 class="mb-3">
                                <i class="bi bi-truck me-2" style="color: #0066cc;"></i> Jadwal Pengiriman
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_po" class="form-label">
                                            <i class="bi bi-calendar me-1" style="color: #0066cc;"></i> Tanggal PO
                                        </label>
                                        <input type="date" name="tanggal_po" id="tanggal_po" 
                                               class="form-control @error('tanggal_po') is-invalid @enderror"
                                               value="{{ old('tanggal_po', date('Y-m-d')) }}" required>
                                        @error('tanggal_po')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_kirim_rencana" class="form-label">
                                            <i class="bi bi-calendar-check me-1" style="color: #0066cc;"></i> Tanggal Pengiriman Rencana
                                        </label>
                                        <input type="date" name="tanggal_kirim_rencana" id="tanggal_kirim_rencana" 
                                               class="form-control @error('tanggal_kirim_rencana') is-invalid @enderror"
                                               value="{{ old('tanggal_kirim_rencana') }}">
                                        @error('tanggal_kirim_rencana')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Notes Section -->
                            <h5 class="mb-3">
                                <i class="bi bi-pencil-square me-2" style="color: #0066cc;"></i> Catatan Tambahan
                            </h5>

                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan / Notes</label>
                                <textarea name="keterangan" id="keterangan" rows="3" 
                                          class="form-control @error('keterangan') is-invalid @enderror"
                                          placeholder="Masukkan keterangan atau catatan tambahan...">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Opsional - Gunakan untuk informasi tambahan</small>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i> Simpan Purchase Order
                                </button>
                                <a href="{{ route('po.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-x-circle me-2"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <i class="bi bi-list-check me-2"></i> Ringkasan
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info d-flex align-items-start" role="alert">
                            <i class="bi bi-info-circle me-2 flex-shrink-0 mt-1"></i>
                            <div>
                                <strong>Informasi:</strong>
                                <p class="mb-0 mt-2 small">Pastikan semua field terisi dengan benar sebelum menyimpan. Anda dapat mengedit PO dalam status Draft.</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Vendor Terpilih</label>
                            <p id="vendorDisplay" class="mb-0 fw-bold">-</p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">BBM Terpilih</label>
                            <p id="bbmDisplay" class="mb-0 fw-bold">-</p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Total Liter</label>
                            <p id="quantityDisplay" class="mb-0 fw-bold fs-5">0 L</p>
                        </div>

                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Harga per Liter</label>
                            <p id="priceDisplay" class="mb-0 fw-bold">Rp 0</p>
                        </div>

                        <div class="bg-light p-3 rounded">
                            <label class="small text-muted text-uppercase fw-bold d-block">Total Nilai</label>
                            <p id="totalDisplay" class="mb-0 fw-bold fs-5" style="color: #0066cc;">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-calculate total value
        const jumlahInput = document.getElementById('jumlah_liter');
        const hargaInput = document.getElementById('harga_per_liter');
        const totalInput = document.getElementById('total_nilai');
        const vendorSelect = document.getElementById('vendor_id');
        const bbmSelect = document.getElementById('bbm_id');

        function calculateTotal() {
            const jumlah = parseFloat(jumlahInput.value) || 0;
            const harga = parseFloat(hargaInput.value) || 0;
            const total = jumlah * harga;
            
            totalInput.value = total.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });

            document.getElementById('totalDisplay').textContent = 
                'Rp ' + total.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
        }

        function updateDisplay() {
            const vendor = vendorSelect.options[vendorSelect.selectedIndex];
            const bbm = bbmSelect.options[bbmSelect.selectedIndex];
            
            document.getElementById('vendorDisplay').textContent = vendor.text || '-';
            document.getElementById('bbmDisplay').textContent = bbm.text || '-';
            document.getElementById('quantityDisplay').textContent = (parseFloat(jumlahInput.value) || 0) + ' L';
            document.getElementById('priceDisplay').textContent = 
                'Rp ' + (parseFloat(hargaInput.value) || 0).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            calculateTotal();
        }

        jumlahInput.addEventListener('input', updateDisplay);
        hargaInput.addEventListener('input', updateDisplay);
        vendorSelect.addEventListener('change', updateDisplay);
        bbmSelect.addEventListener('change', updateDisplay);

        // Initialize display
        updateDisplay();
    </script>
</x-app-layout>
