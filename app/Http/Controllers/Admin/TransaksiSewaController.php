<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Denda;
use App\Models\Deposit;
use App\Models\KondisiKendaraan;
use App\Models\MetodePembayaran;
use App\Models\Pembayaran;
use App\Models\TransaksiSewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TransaksiSewaController extends Controller
{
    // ===================== MONITORING (Read & View) =====================

    public function index(Request $request)
    {
        $query = TransaksiSewa::with(['booking.pelanggan.user', 'booking.kendaraan', 'kasir']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode_transaksi', 'like', "%{$request->search}%")
                ->orWhereHas('booking.pelanggan.user', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                ->orWhereHas('kasir', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $transaksi = $query->latest()->paginate(15);

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function show(TransaksiSewa $transaksi)
    {
        $transaksi->load([
            'booking.pelanggan.user',
            'booking.kendaraan',
            'kasir',
            'pembayaran.metodePembayaran',
            'deposit',
            'denda',
            'kondisiKendaraan',
        ]);

        return view('admin.transaksi.show', compact('transaksi'));
    }


    // ===================== SERAH TERIMA (Booking → Transaksi) =====================

    public function formSerahTerima(Booking $booking)
    {
        if ($booking->status !== 'disetujui') {
            return back()->with('error', 'Booking tidak dapat diproses karena belum disetujui admin.');
        }

        $booking->load(['pelanggan.user', 'kendaraan.kategori']);
        $metode = MetodePembayaran::where('is_active', true)->get();

        return view('admin.transaksi.serah-terima', compact('booking', 'metode'));
    }

    public function prosesSerahTerima(Request $request, Booking $booking)
    {
        $request->validate([
            'tanggal_ambil_aktual' => 'required|date',
            'bahan_bakar_awal'     => 'required|string|max:50',
            'km_odometer_awal'     => 'required|integer|min:0',
            'foto_kondisi'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'catatan_kondisi'      => 'nullable|string',
            'jumlah_deposit'       => 'required|numeric|min:0',
            'metode_pembayaran_id' => 'required|exists:metode_pembayaran,id',
            'jumlah_bayar'         => 'required|numeric|min:0',
            'catatan_kasir'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $booking) {
            $kodeTransaksi = 'TRX-' . strtoupper(Str::random(10));

            $transaksi = TransaksiSewa::create([
                'kode_transaksi'       => $kodeTransaksi,
                'booking_id'           => $booking->id,
                'kasir_id'             => Auth::id(), // Tercatat sebagai Admin yang mengeksekusi
                'tanggal_ambil_aktual' => $request->tanggal_ambil_aktual,
                'total_biaya'          => $booking->estimasi_biaya,
                'total_denda'          => 0,
                'total_bayar'          => $booking->estimasi_biaya,
                'status'               => 'berjalan',
                'catatan_kasir'        => $request->catatan_kasir,
            ]);

            $fotoPath = null;
            if ($request->hasFile('foto_kondisi')) {
                $fotoPath = $request->file('foto_kondisi')->store('kondisi', 'public');
            }

            KondisiKendaraan::create([
                'transaksi_id'    => $transaksi->id,
                'waktu'           => 'sebelum',
                'foto'            => $fotoPath,
                'bahan_bakar'     => $request->bahan_bakar_awal,
                'km_odometer'     => $request->km_odometer_awal,
                'catatan_kondisi' => $request->catatan_kondisi,
                'dicatat_oleh'    => Auth::id(),
            ]);

            if ($request->jumlah_deposit > 0) {
                Deposit::create([
                    'transaksi_id'    => $transaksi->id,
                    'jumlah'          => $request->jumlah_deposit,
                    'status'          => 'ditahan',
                    'jumlah_dipotong' => 0,
                ]);
            }

            if ($request->jumlah_bayar > 0) {
                Pembayaran::create([
                    'transaksi_id'         => $transaksi->id,
                    'metode_pembayaran_id' => $request->metode_pembayaran_id,
                    'jumlah_bayar'         => $request->jumlah_bayar,
                    'jumlah_kembali'       => 0,
                    'status'               => 'lunas',
                ]);
            }

            $booking->kendaraan->update(['status' => 'disewa']);
            $booking->update(['status' => 'aktif']);
        });

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Kendaraan berhasil diserahkan oleh Admin. Transaksi sewa dimulai.');
    }


    // ===================== PENGEMBALIAN & KONDISI AKHIR =====================

    public function formPengembalian(TransaksiSewa $transaksi)
    {
        if ($transaksi->status !== 'berjalan') {
            return back()->with('error', 'Transaksi tidak dalam status aktif/berjalan.');
        }

        $transaksi->load(['booking.pelanggan.user', 'booking.kendaraan', 'deposit', 'pembayaran']);
        $metode = MetodePembayaran::where('is_active', true)->get();

        return view('admin.transaksi.pengembalian', compact('transaksi', 'metode'));
    }

    public function prosesPengembalian(Request $request, TransaksiSewa $transaksi)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'bahan_bakar_akhir'      => 'required|string|max:50',
            'km_odometer_akhir'      => 'required|integer|min:0',
            'foto_kondisi_akhir'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'catatan_kondisi_akhir'  => 'nullable|string',
            
            'dendas'                 => 'nullable|array',
            'dendas.*.jenis_denda'   => 'required|string',
            'dendas.*.total_denda'   => 'required|numeric|min:0',
            'dendas.*.jumlah_hari_telat' => 'nullable|integer|min:0',
            'dendas.*.tarif_denda'   => 'nullable|numeric|min:0',
            'dendas.*.keterangan'    => 'nullable|string',
            
            'alasan_potongan'        => 'nullable|string',
            'metode_pembayaran_id'   => 'required_if:jumlah_bayar,>0|exists:metode_pembayaran,id',
            'jumlah_bayar'           => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $transaksi) {
            $totalDenda = 0;

            $fotoPath = null;
            if ($request->hasFile('foto_kondisi_akhir')) {
                $fotoPath = $request->file('foto_kondisi_akhir')->store('kondisi', 'public');
            }

            KondisiKendaraan::create([
                'transaksi_id'    => $transaksi->id,
                'waktu'           => 'sesudah',
                'foto'            => $fotoPath,
                'bahan_bakar'     => $request->bahan_bakar_akhir,
                'km_odometer'     => $request->km_odometer_akhir,
                'catatan_kondisi' => $request->catatan_kondisi_akhir,
                'dicatat_oleh'    => Auth::id(),
            ]);

            if ($request->has('dendas') && is_array($request->dendas)) {
                foreach ($request->dendas as $dendaItem) {
                    $itemTotal = floatval($dendaItem['total_denda'] ?? 0);
                    
                    if ($itemTotal > 0) {
                        $totalDenda += $itemTotal;

                        Denda::create([
                            'transaksi_id'     => $transaksi->id,
                            'jenis_denda'      => $dendaItem['jenis_denda'],
                            'keterangan'       => $dendaItem['keterangan'] ?? null,
                            'jumlah_hari_telat' => $dendaItem['jumlah_hari_telat'] ?? 0,
                            'tarif_denda'      => $dendaItem['tarif_denda'] ?? 0,
                            'total_denda'      => $itemTotal,
                        ]);
                    }
                }
            }

            $deposit = $transaksi->deposit;
            $potonganDepositOtomatis = 0;

            if ($deposit) {
                $jumlahDepositAwal = $deposit->jumlah;
                $potonganDepositOtomatis = min($totalDenda, $jumlahDepositAwal);
                
                $alasanKasir = $request->filled('alasan_potongan') 
                    ? $request->alasan_potongan 
                    : "Potongan denda pemakaian.";

                $alasanFinal = $potonganDepositOtomatis > 0 
                    ? $alasanKasir . " (Terpotong denda otomatis oleh Admin: Rp " . number_format($potonganDepositOtomatis, 0, ',', '.') . ")"
                    : "Kembali penuh. Selesai tanpa denda.";

                $statusBaruDeposit = $potonganDepositOtomatis >= $jumlahDepositAwal ? 'dipotong' : 'dikembalikan';

                $deposit->update([
                    'status'          => $statusBaruDeposit,
                    'jumlah_dipotong' => $potonganDepositOtomatis,
                    'alasan_potongan' => $alasanFinal,
                    'dikembalikan_at' => now(),
                ]);
            }

            $totalSewaDanDendaBersih = ($transaksi->total_biaya + $totalDenda) - $potonganDepositOtomatis;
            $yangSudahDibayar = $transaksi->pembayaran()->where('status', 'lunas')->sum('jumlah_bayar');
            $sisaKekurangan    = max(0, $totalSewaDanDendaBersih - $yangSudahDibayar);
            $kembalian = max(0, $request->jumlah_bayar - $sisaKekurangan);

            if ($request->jumlah_bayar > 0) {
                Pembayaran::create([
                    'transaksi_id'         => $transaksi->id,
                    'metode_pembayaran_id' => $request->metode_pembayaran_id,
                    'jumlah_bayar'         => $request->jumlah_bayar,
                    'jumlah_kembali'       => $kembalian,
                    'status'               => 'lunas',
                ]);
            }

            $transaksi->update([
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'total_denda'            => $totalDenda,
                'total_bayar'            => max(0, $totalSewaDanDendaBersih), 
                'status'                 => 'selesai',
            ]);

            $transaksi->booking->kendaraan->update(['status' => 'tersedia']);
            $transaksi->booking->update(['status' => 'selesai']);
        });

        return redirect()->route('admin.transaksi.show', $transaksi)
            ->with('success', 'Pengembalian kendaraan berhasil diproses oleh Admin. Unit kembali tersedia.');
    }

    // ===================== SELESAI / DATA DELETION =====================

    public function destroy(TransaksiSewa $transaksi)
    {
        DB::transaction(function() use ($transaksi) {
            // 1. Kembalikan status armada kendaraan menjadi tersedia kembali
            if ($transaksi->booking && $transaksi->booking->kendaraan) {
                $transaksi->booking->kendaraan->update(['status' => 'tersedia']);
            }

            // 2. Kembalikan status booking menjadi disetujui (agar bisa diproses ulang jika salah input)
            if ($transaksi->booking) {
                $transaksi->booking->update(['status' => 'disetujui']);
            }

            // 3. Hapus data transaksi (Relasi seperti kondisi_kendaraan, pembayaran, denda, deposit akan ikut terhapus/cascade sesuai konfigurasi migration Anda)
            $transaksi->delete();
        });

        return redirect()->route('admin.transaksi.index')
            ->with('success', 'Data transaksi berhasil dihapus oleh Admin dan status armada dipulihkan.');
    }
}