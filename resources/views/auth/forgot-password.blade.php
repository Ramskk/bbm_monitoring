<x-guest-layout>
    <div class="auth-card">
        <div class="auth-header">
            <h1>
                <i class="bi bi-lock"></i>
                Lupa Password
            </h1>
            <p>Reset password akun Anda</p>
        </div>

        <div class="auth-body">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i>
                <small>Masukkan email akun Anda, dan kami akan mengirimkan link untuk mereset password.</small>
            </div>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        placeholder="Masukkan email Anda"
                    >
                    @error('email')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-auth btn-primary-auth">
                        <i class="bi bi-envelope-check"></i> Kirim Link Reset
                    </button>
                </div>

                <div class="text-center" style="font-size: 0.9rem;">
                    <a href="{{ route('login') }}" style="color: #0066cc; text-decoration: none; font-weight: 600;">
                        <i class="bi bi-arrow-left"></i> Kembali ke login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
