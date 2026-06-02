<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ===================== LOGIN =====================

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return $this->redirectByRole($user->role);
    }

    // ===================== REGISTER (Pelanggan) =====================

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'pelanggan',
            'is_active' => true,
        ]);

        // Buat record pelanggan kosong (akan dilengkapi di profil)
        Pelanggan::create([
            'user_id'           => $user->id,
            'status_verifikasi' => 'pending',
        ]);

        Auth::login($user);

        return redirect()->route('pelanggan.dashboard')
            ->with('success', 'Registrasi berhasil! Silakan lengkapi profil Anda.');
    }

    // ===================== LOGOUT =====================

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ===================== HELPERS =====================

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'kasir'     => redirect()->route('kasir.dashboard'),
            'pelanggan' => redirect()->route('pelanggan.dashboard'),
            default     => redirect('/'),
        };
    }
}
