<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransaksiSewa;
use App\Models\Deposit;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function pendapatan(Request $request)
    {
        $request->validate([
            'dari'   => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ]);

        // Set default range tanggal (Awal bulan s/d hari ini)
        $dari   = $request->dari   ?? now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $start = \Carbon\Carbon::parse($dari)->startOfDay();
        $end   = \Carbon\Carbon::parse($sampai)->endOfDay();

        // Mengambil semua transaksi baik yang sedang berjalan maupun selesai untuk melihat arus jaminan/deposit
        $transaksi = TransaksiSewa::with(['booking.pelanggan.user', 'booking.kendaraan', 'kasir'])
            ->whereIn('status', ['berjalan', 'selesai'])
            ->whereBetween('created_at', [$start, $end]) // Berdasarkan waktu pencatatan kas masuk/keluar
            ->latest()
            ->get();

        $depost = Deposit::with('transaksiSewa.booking.kendaraan')
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        // Kalkulasi Rekap Finansial Arus Kas Lengkap
        $summary = [
            'total_transaksi'        => $transaksi->count(),
            'transaksi_selesai'      => $transaksi->where('status', 'selesai')->count(),
            'transaksi_aktif'        => $transaksi->where('status', 'berjalan')->count(),
            
            // 1. Omset Kotor Operasional (Biaya Sewa Murni + Denda Terwujud)
            'total_biaya_sewa'       => $transaksi->sum('total_biaya'),
            'total_denda'            => $transaksi->where('status', 'selesai')->sum('total_denda'),
            
            // 2. Alur Deposit / Jaminan (Diambil dari kolom jaminan/pembayaran awal di db Anda)
            // Catatan: Sesuai struktur db_rental_mobil, uang titipan awal masuk sebagai komponen penjamin
            // 'total_deposit_masuk'    => $transaksi->sum('total_biaya'), // Mengasumsikan deposit flat 10% jika tidak ada kolom khusus deposit, atau sesuaikan dengan nominal jaminan di DB Anda
            'total_deposit_kembali'  => $transaksi->where('status', 'selesai')->sum('total_biaya') * 0.10, 
            
            'total_deposit_masuk' => Deposit::sum('jumlah'),
            'total_deposit_potong' => Deposit::sum('jumlah_dipotong'),
            'total_deposit_dikembalikan' => Deposit::where('status', 'dikembalikan')->sum('jumlah'),

            // 3. Total Kas Masuk Bersih (Riil yang sudah dibayarkan dan sah menjadi milik rental)
            'net_pendapatan'         => $transaksi->where('status', 'selesai')->sum('total_bayar'),
        ];

        // Hitung sisa deposit setelah summary terbangun
        $summary['sisa_deposit'] = $summary['total_deposit_masuk']
                        - $summary['total_deposit_potong']
                        - $summary['total_deposit_dikembalikan'];

        return view('admin.laporan.pendapatan', compact('transaksi', 'summary', 'dari', 'sampai'));
    }

    public function kendaraan(Request $request)
    {
        $dari   = $request->dari   ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai = $request->sampai ?? Carbon::now()->toDateString();

        $start = Carbon::parse($dari)->startOfDay();
        $end   = Carbon::parse($sampai)->endOfDay();

        $data = TransaksiSewa::with('booking.kendaraan')
            ->where('status', 'selesai')
            ->whereBetween('tanggal_kembali_aktual', [$start, $end])
            ->get()
            ->groupBy('booking.kendaraan_id')
            ->map(function ($transaksi) {
                $kendaraan = $transaksi->first()->booking->kendaraan;
                return [
                    'kendaraan'        => $kendaraan,
                    'jumlah_sewa'      => $transaksi->count(),
                    'total_pendapatan' => $transaksi->sum('total_bayar'),
                ];
            })
            ->sortByDesc('jumlah_sewa')
            ->values();

        return view('admin.laporan.kendaraan', compact('data', 'dari', 'sampai'));
    }

    public function auditLog(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');

        // Filter Pencarian Berdasarkan User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter Pencarian Berdasarkan Jenis Aksi
        if ($request->filled('aksi')) {
            $query->where('aksi', 'LIKE', '%' . $request->aksi . '%');
        }

        $logs = $query->paginate(30)->withQueryString();
        
        // Mengambil daftar user untuk opsi dropdown filter di View
        $users = User::whereIn('role', ['admin', 'kasir'])->orderBy('name')->get();

        return view('admin.laporan.audit-log', compact('logs', 'users'));
    }
}