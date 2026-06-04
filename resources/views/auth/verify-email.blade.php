<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h1>
                <i class="bi bi-envelope-check"></i>
                Verifikasi Email
            </h1>
            <p>Selesaikan proses registrasi</p>
        </div>

        <div class="auth-body">
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i>
                <small>Terima kasih telah mendaftar! Sebelum memulai, silahkan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan. Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan yang lain.</small>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <strong>Berhasil!</strong> Tautan verifikasi baru telah dikirim ke alamat email Anda.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('verification.send') }}" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-auth btn-primary-auth">
                        <i class="bi bi-send"></i> Kirim Ulang Email Verifikasi
                    </button>
                </form>
            </div>

            <div class="text-center mt-3" style="font-size: 0.9rem; border-top: 1px solid #e9ecef; padding-top: 1rem;">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #0066cc; font-weight: 600; cursor: pointer; text-decoration: none;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
