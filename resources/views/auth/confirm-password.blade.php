<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h1>
                <i class="bi bi-shield-lock"></i>
                Konfirmasi Password
            </h1>
            <p>Keamanan akun Anda</p>
        </div>

        <div class="auth-body">
            <div class="alert alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <small>Ini adalah area aman aplikasi. Silahkan konfirmasi password Anda sebelum melanjutkan.</small>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <strong>Password Salah!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <input 
                        id="password" 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Masukkan password Anda"
                    >
                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-auth btn-primary-auth">
                    <i class="bi bi-check-circle"></i> Konfirmasi
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
