<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Pelanggan;
use App\Models\Booking;
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
        if (str_contains(config('app.url'), '.ngrok-free.app')) {
            URL::forceScheme('https');
        }

        // Mengirimkan data hitungan pelanggan pending hanya ke view sidebar-admin
        View::composer('components.sidebar-admin', function ($view) {
            $pendingCount = Pelanggan::where('status_verifikasi', 'pending')->count();
            $view->with('pendingPelangganCount', $pendingCount);
        });

        View::composer('components.sidebar-admin', function($view){
            $bookingCount = Booking::where('status', 'pending')->count();
            $view->with('bookingPelangganCount', $bookingCount);
        });
        
    }
}
