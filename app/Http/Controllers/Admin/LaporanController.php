<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Pembayaran;
use App\Models\TransaksiSewa;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function pendapatan(Request $request)
    {
        $request->validate([
            'dari'   => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ]);

        $dari   = $request->dari   ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $transaksi = TransaksiSewa::with(['booking.pelanggan.user', 'booking.kendaraan', 'kasir'])
            ->where('status', 'selesai')
            ->whereBetween('tanggal_kembali_aktual', [$dari, $sampai . ' 23:59:59'])
            ->get();

        $summary = [
            'total_transaksi'  => $transaksi->count(),
            'total_pendapatan' => $transaksi->sum('total_bayar'),
            'total_denda'      => $transaksi->sum('total_denda'),
        ];

        return view('admin.laporan.pendapatan', compact('transaksi', 'summary', 'dari', 'sampai'));
    }

    public function kendaraan(Request $request)
    {
        $dari   = $request->dari   ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $data = TransaksiSewa::with('booking.kendaraan')
            ->where('status', 'selesai')
            ->whereBetween('tanggal_kembali_aktual', [$dari, $sampai . ' 23:59:59'])
            ->get()
            ->groupBy('booking.kendaraan_id')
            ->map(function ($transaksi) {
                $kendaraan = $transaksi->first()->booking->kendaraan;
                return [
                    'kendaraan'       => $kendaraan,
                    'jumlah_sewa'     => $transaksi->count(),
                    'total_pendapatan' => $transaksi->sum('total_bayar'),
                ];
            })
            ->sortByDesc('jumlah_sewa')
            ->values();

        return view('admin.laporan.kendaraan', compact('data', 'dari', 'sampai'));
    }

    public function auditLog(Request $request)
    {
        $query = \App\Models\AuditLog::with('user')->latest('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        $logs = $query->paginate(30);

        return view('admin.laporan.audit-log', compact('logs'));
    }
}
