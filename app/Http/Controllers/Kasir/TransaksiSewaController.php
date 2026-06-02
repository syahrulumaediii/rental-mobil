<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Denda;
use App\Models\Deposit;
use App\Models\DetailTransaksi;
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
    public function index(Request $request)
    {
        $query = TransaksiSewa::with(['booking.pelanggan.user', 'booking.kendaraan', 'kasir']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode_transaksi', 'like', "%{$request->search}%")
                ->orWhereHas('booking.pelanggan.user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $transaksi = $query->latest()->paginate(15);

        return view('kasir.transaksi.index', compact('transaksi'));
    }

    // ===================== SERAH TERIMA (Booking → Transaksi) =====================

    public function formSerahTerima(Booking $booking)
    {
        if ($booking->status !== 'disetujui') {
            return back()->with('error', 'Booking tidak dapat diproses.');
        }

        $booking->load(['pelanggan.user', 'kendaraan.kategori']);
        $metode = MetodePembayaran::active()->get();

        return view('kasir.transaksi.serah-terima', compact('booking', 'metode'));
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
            $kodeTransaksi = 'TRX-' . strtoupper(Str::random(8));

            // Buat transaksi sewa
            $transaksi = TransaksiSewa::create([
                'kode_transaksi'       => $kodeTransaksi,
                'booking_id'           => $booking->id,
                'kasir_id'             => Auth::id(),
                'tanggal_ambil_aktual' => $request->tanggal_ambil_aktual,
                'total_biaya'          => $booking->estimasi_biaya,
                'total_denda'          => 0,
                'total_bayar'          => $booking->estimasi_biaya,
                'status'               => 'berjalan',
                'catatan_kasir'        => $request->catatan_kasir,
            ]);

            // Catat kondisi awal kendaraan
            $fotoPath = null;
            if ($request->hasFile('foto_kondisi')) {
                $fotoPath = $request->file('foto_kondisi')->store('kondisi', 'public');
            }

            KondisiKendaraan::create([
                'transaksi_id'   => $transaksi->id,
                'waktu'          => 'penyerahan',
                'foto'           => $fotoPath,
                'bahan_bakar'    => $request->bahan_bakar_awal,
                'km_odometer'    => $request->km_odometer_awal,
                'catatan_kondisi' => $request->catatan_kondisi,
                'dicatat_oleh'   => Auth::id(),
            ]);

            // Catat deposit
            Deposit::create([
                'transaksi_id' => $transaksi->id,
                'jumlah'       => $request->jumlah_deposit,
                'status'       => 'ditahan',
            ]);

            // Catat pembayaran awal (jika ada uang muka)
            if ($request->jumlah_bayar > 0) {
                Pembayaran::create([
                    'transaksi_id'        => $transaksi->id,
                    'metode_pembayaran_id' => $request->metode_pembayaran_id,
                    'jumlah_bayar'        => $request->jumlah_bayar,
                    'jumlah_kembali'      => 0,
                    'status'              => 'berhasil',
                ]);
            }

            // Update status booking
            $booking->update(['status' => 'berlangsung']);
        });

        return redirect()->route('kasir.transaksi.index')
            ->with('success', 'Kendaraan berhasil diserahkan. Transaksi dimulai.');
    }

    // ===================== PENGEMBALIAN =====================

    public function show(TransaksiSewa $transaksi)
    {
        $transaksi->load([
            'booking.pelanggan.user',
            'booking.kendaraan',
            'kasir',
            'detailTransaksi',
            'pembayaran.metodePembayaran',
            'deposit',
            'denda',
            'kondisiKendaraan',
        ]);

        return view('kasir.transaksi.show', compact('transaksi'));
    }

    public function formPengembalian(TransaksiSewa $transaksi)
    {
        if ($transaksi->status !== 'berjalan') {
            return back()->with('error', 'Transaksi tidak dalam status berjalan.');
        }

        $transaksi->load(['booking.pelanggan.user', 'booking.kendaraan', 'deposit']);
        $metode = MetodePembayaran::active()->get();

        return view('kasir.transaksi.pengembalian', compact('transaksi', 'metode'));
    }

    public function prosesPengembalian(Request $request, TransaksiSewa $transaksi)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'bahan_bakar_akhir'      => 'required|string|max:50',
            'km_odometer_akhir'      => 'required|integer|min:0',
            'foto_kondisi_akhir'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'catatan_kondisi_akhir'  => 'nullable|string',
            'jenis_denda'            => 'nullable|string',
            'keterangan_denda'       => 'nullable|string',
            'jumlah_jam_telat'       => 'nullable|integer|min:0',
            'tarif_denda'            => 'nullable|numeric|min:0',
            'total_denda'            => 'nullable|numeric|min:0',
            'potongan_deposit'       => 'nullable|numeric|min:0',
            'alasan_potongan'        => 'nullable|string',
            'metode_pembayaran_id'   => 'required|exists:metode_pembayaran,id',
            'jumlah_bayar'           => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $transaksi) {
            $totalDenda = $request->total_denda ?? 0;

            // Catat kondisi akhir kendaraan
            $fotoPath = null;
            if ($request->hasFile('foto_kondisi_akhir')) {
                $fotoPath = $request->file('foto_kondisi_akhir')->store('kondisi', 'public');
            }

            KondisiKendaraan::create([
                'transaksi_id'    => $transaksi->id,
                'waktu'           => 'pengembalian',
                'foto'            => $fotoPath,
                'bahan_bakar'     => $request->bahan_bakar_akhir,
                'km_odometer'     => $request->km_odometer_akhir,
                'catatan_kondisi' => $request->catatan_kondisi_akhir,
                'dicatat_oleh'    => Auth::id(),
            ]);

            // Catat denda jika ada
            if ($totalDenda > 0 && $request->filled('jenis_denda')) {
                Denda::create([
                    'transaksi_id'   => $transaksi->id,
                    'jenis_denda'    => $request->jenis_denda,
                    'keterangan'     => $request->keterangan_denda,
                    'jumlah_jam_telat' => $request->jumlah_jam_telat ?? 0,
                    'tarif_denda'    => $request->tarif_denda ?? 0,
                    'total_denda'    => $totalDenda,
                ]);
            }

            // Update / kembalikan deposit
            $deposit = $transaksi->deposit;
            if ($deposit) {
                $potongan = $request->potongan_deposit ?? 0;
                $deposit->update([
                    'status'          => 'dikembalikan',
                    'jumlah_dipotong' => $potongan,
                    'alasan_potongan' => $request->alasan_potongan,
                    'dikembalikan_at' => now(),
                ]);
            }

            // Catat pembayaran pelunasan
            $kembalian = max(0, $request->jumlah_bayar - ($transaksi->total_biaya + $totalDenda - $transaksi->pembayaran->sum('jumlah_bayar')));
            Pembayaran::create([
                'transaksi_id'         => $transaksi->id,
                'metode_pembayaran_id' => $request->metode_pembayaran_id,
                'jumlah_bayar'         => $request->jumlah_bayar,
                'jumlah_kembali'       => $kembalian,
                'status'               => 'berhasil',
            ]);

            // Update transaksi
            $transaksi->update([
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'total_denda'            => $totalDenda,
                'total_bayar'            => $transaksi->total_biaya + $totalDenda,
                'status'                 => 'selesai',
            ]);

            // Kembalikan status kendaraan
            $transaksi->booking->kendaraan->update(['status' => 'tersedia']);
            $transaksi->booking->update(['status' => 'selesai']);
        });

        return redirect()->route('kasir.transaksi.show', $transaksi)
            ->with('success', 'Pengembalian kendaraan berhasil diproses.');
    }
}
