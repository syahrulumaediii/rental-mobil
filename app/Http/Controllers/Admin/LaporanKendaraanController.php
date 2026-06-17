<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\KategoriKendaraan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanKendaraanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data Kategori untuk dropdown filter
        $kategori = KategoriKendaraan::all();

        // 2. Inisialisasi Filter Periode Pengukuran Produktivitas
        $dari   = $request->filled('dari') ? $request->dari : Carbon::now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : Carbon::now()->endOfMonth()->toDateString();

        // 3. Query Utama Kendaraan (Perbaikan kurung siku pada withSum)
        $query = Kendaraan::with('kategori')
            ->withCount(['bookings as total_disewa' => function ($q) use ($dari, $sampai) {
                // Spesifikasikan booking.status agar tidak tabrakan dengan status milik kendaraan
                $q->whereIn('booking.status', ['aktif', 'selesai'])
                    ->whereBetween('booking.tanggal_mulai', [$dari, $sampai]);
            }])
            ->withSum(['bookings as omset_kendaraan' => function ($q) use ($dari, $sampai) {
                // Spesifikasikan nama tabel pada join dan kondisi where
                $q->join('transaksi_sewa', 'booking.id', '=', 'transaksi_sewa.booking_id')
                    ->where('transaksi_sewa.status', 'selesai')
                    ->whereBetween('transaksi_sewa.tanggal_ambil_aktual', [$dari, $sampai]);
            }], 'transaksi_sewa.total_biaya'); // <--- PERBAIKAN: Kurung siku dipindah sebelum koma ini
        // 4. Implementasi Filter Opsional Tambahan
        if ($request->filled('kategori_id')) {
            $query->where('kendaraan.kategori_id', $request->kategori_id);
        }

        if ($request->filled('status')) {
            // PERBAIKAN: Beri awalan 'kendaraan.status' karena filter ini mencari status fisik mobilnya saat ini
            $query->where('kendaraan.status', $request->status);
        }

        $kendaraan = $query->orderBy('total_disewa', 'desc')->get();

        // 5. Perhitungan Metrik Summary Dashboard Laporan
        $summary = [
            'total_unit'       => Kendaraan::count(),
            'unit_aktif'    => Kendaraan::where('status', 'aktif')->count(),
            'unit_disewa'      => Kendaraan::where('status', 'disewa')->count(),
            'unit_perawatan'   => Kendaraan::where('status', 'servis')->count(),
            'total_omset_sewa' => $kendaraan->sum('omset_kendaraan'),
        ];

        return view('admin.laporan.kendaraan', compact('kendaraan', 'kategori', 'dari', 'sampai', 'summary'));
    }
}
