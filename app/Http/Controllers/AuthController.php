<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kendaraan;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Authenticate the user.
     */
    public function login(Request $request)
    {
        // Rate limiting: 5 attempts per minute per IP
        $key = 'login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('email'))
                ->with('error', "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.");
        }

        // Cek autentikasi (Auth::attempt mengembalikan nilai BOOLEAN true/false)
        $loginSukses = Auth::attempt($request->only('email', 'password'), $request->boolean('remember'));

        if (!$loginSukses) {
            RateLimiter::hit($key);
            $request->session()->flash('error', 'Email atau password salah.');
            return redirect()->route('login')->withInput();
        }

        // Ambil objek User yang asli setelah berhasil login
        $user = Auth::user();

        // Cek apakah akun aktif
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->flash('error', 'Akun Anda tidak aktif. Hubungi admin.');
            return redirect()->route('login');
        }

        // Update last login (Pastikan method ini ada di Model User Anda)
        if (method_exists($user, 'updateLastLogin')) {
            $user->updateLastLogin($request->ip());
        }

        // Audit log
        AuditLogService::log('login', null, $user->id, null, ['ip' => $request->ip()], 'User login');

        // Regenerate session agar aman dari session fixation
        $request->session()->regenerate();

        // Clear rate limiter on success
        RateLimiter::clear($key);

        return redirect()->route('dashboard');
    }

    /**
     * Log out the user.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Catat log dulu sebelum datanya hilang saat logout
        if (Auth::check()) {
            $user = Auth::user();
            AuditLogService::log('logout', null, $user->id, null, null, 'User logout');
        }

        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}