<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        $stats = [
            'total_booking'    => $pelanggan?->booking()->count() ?? 0,
            'booking_aktif'    => $pelanggan?->booking()->whereIn('status', ['pending', 'disetujui', 'berlangsung'])->count() ?? 0,
            'sewa_selesai'     => $pelanggan?->booking()->where('status', 'selesai')->count() ?? 0,
            'is_verified'      => $pelanggan?->isVerified() ?? false,
            'is_blacklisted'   => $pelanggan?->isBlacklisted() ?? false,
        ];

        $booking_terbaru = $pelanggan?->booking()
            ->with('kendaraan')
            ->latest()
            ->take(5)
            ->get();

        $notifikasi = $user->notifikasi()->unread()->latest()->take(5)->get();

        return view('pelanggan.dashboard.index', compact('stats', 'booking_terbaru', 'notifikasi'));
    }
}
