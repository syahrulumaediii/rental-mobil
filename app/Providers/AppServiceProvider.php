<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Pelanggan;
use App\Models\Booking;
use App\Models\TransaksiSewa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL; // 👈 Pastikan baris ini ada

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // 🔴 Tambahkan kode ini untuk memaksa HTTPS jika menggunakan ngrok
        // if (str_contains(config('app.url'), '.ngrok-free.app')) {
        //     URL::forceScheme('https');
        // }


        // 1. Notifikasi Transaksi Pending (Admin)

        // Mengirimkan data hitungan pelanggan pending hanya ke view sidebar-admin
        View::composer('components.sidebar-admin', function ($view) {
            $pendingCount = Pelanggan::where('status_verifikasi', 'pending')->count();
            $view->with('pendingPelangganCount', $pendingCount);
        });

        View::composer('components.sidebar-admin', function ($view) {
            $bookingCount = Booking::where('status', 'pending')->count();
            $view->with('bookingPelangganCount', $bookingCount);
        });


        // 2. Notifikasi di Kasir

        // 
        View::composer('components.sidebar-kasir', function ($view) {
            $bookingCount = 0;
            $telatCount = 0;

            if (Auth::check() && Auth::user()->role === 'kasir') {
                // Hitung Booking Pending (untuk konfirmasi)
                $bookingCount = Booking::where('status', 'pending')->count();

                // Hitung Transaksi Telat (untuk monitor)
                $telatCount = TransaksiSewa::where('status', 'berjalan')
                    ->whereHas('booking', fn($q) => $q->where('tanggal_selesai', '<', now()))
                    ->count();
            }

            $view->with([
                'bookingPelangganCount' => $bookingCount,
                'telatCount' => $telatCount
            ]);
        });


        // View Composer untuk semua view yang menggunakan layouts.app
        View::composer(['layouts.app', 'components.sidebar-kasir', 'kasir.transaksi.index'], function ($view) {
            $bookingCount = 0;
            $telatCount = 0;

            if (Auth::check() && Auth::user()->role === 'kasir') {
                $bookingCount = Booking::where('status', 'pending')->count();
                $telatCount = TransaksiSewa::where('status', 'berjalan')
                    ->whereHas('booking', fn($q) => $q->where('tanggal_selesai', '<', now()))
                    ->count();
            }

            $view->with([
                'bookingCount' => $bookingCount,
                'telatCount'   => $telatCount,
                'totalNotif'   => $bookingCount + $telatCount
            ]);
        });
    }
}
