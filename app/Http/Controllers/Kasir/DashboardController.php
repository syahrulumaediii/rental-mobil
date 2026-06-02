<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TransaksiSewa;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'booking_siap_ambil'  => Booking::where('status', 'disetujui')->count(),
            'transaksi_aktif'     => TransaksiSewa::where('status', 'berjalan')->count(),
            'transaksi_hari_ini'  => TransaksiSewa::whereDate('created_at', today())->count(),
            'pendapatan_hari_ini' => TransaksiSewa::where('status', 'selesai')
                ->whereDate('tanggal_kembali_aktual', today())
                ->sum('total_bayar'),
        ];

        $booking_siap = Booking::with(['pelanggan.user', 'kendaraan'])
            ->where('status', 'disetujui')
            ->latest()
            ->take(5)
            ->get();

        $transaksi_berjalan = TransaksiSewa::with(['booking.pelanggan.user', 'booking.kendaraan'])
            ->where('status', 'berjalan')
            ->latest()
            ->take(5)
            ->get();

        return view('kasir.dashboard.index', compact('stats', 'booking_siap', 'transaksi_berjalan'));
    }
}
