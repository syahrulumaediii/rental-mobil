<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\TransaksiSewa;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_kendaraan'     => Kendaraan::count(),
            'kendaraan_tersedia'  => Kendaraan::where('status', 'tersedia')->count(),
            'total_pelanggan'     => Pelanggan::count(),
            'pelanggan_verified'  => Pelanggan::where('status_verifikasi', 'verified')->count(),
            'booking_pending'     => Booking::where('status', 'pending')->count(),
            'transaksi_aktif'     => TransaksiSewa::where('status', 'berjalan')->count(),
            'total_pendapatan'    => TransaksiSewa::where('status', 'selesai')->sum('total_bayar'),
            
            // 🔥 TAMBAHAN: Menghitung jumlah booking yang siap diambil (status disetujui)
            'booking_siap_ambil'  => Booking::where('status', 'disetujui')->count(),
        ];

        $booking_terbaru = Booking::with(['pelanggan.user', 'kendaraan'])
            ->latest()
            ->take(5)
            ->get();

        $transaksi_terbaru = TransaksiSewa::with(['booking.pelanggan.user', 'booking.kendaraan', 'kasir'])
            ->latest()
            ->take(5)
            ->get();

        // 🔥 TAMBAHAN: Mengambil 5 data detail booking yang siap diambil untuk tabel dashboard
        $booking_siap_ambil = Booking::with(['pelanggan.user', 'kendaraan'])
            ->where('status', 'disetujui')
            ->orderBy('tanggal_mulai', 'asc') // Urutkan dari tanggal sewa paling dekat
            ->take(5)
            ->get();

        // Jangan lupa masukkan 'booking_siap_ambil' ke dalam fungsi compact()
        return view('admin.dashboard.index', compact(
            'stats', 
            'booking_terbaru', 
            'transaksi_terbaru', 
            'booking_siap_ambil'
        ));
    }
}