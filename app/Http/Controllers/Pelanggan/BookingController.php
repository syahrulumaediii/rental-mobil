<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = Auth::user()->pelanggan;

        $query = $pelanggan->booking()->with('kendaraan');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $booking = $query->latest()->paginate(10);

        return view('pelanggan.booking.index', compact('booking'));
    }

    public function katalog(Request $request)
    {
        $query = Kendaraan::with('kategori')->where('status', 'tersedia');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('transmisi')) {
            $query->where('transmisi', $request->transmisi);
        }

        if ($request->filled('max_tarif')) {
            $query->where('tarif_harian', '<=', $request->max_tarif);
        }

        $kendaraan = $query->latest()->paginate(12);

        return view('pelanggan.booking.katalog', compact('kendaraan'));
    }

    public function create(Kendaraan $kendaraan)
    {
        $pelanggan = Auth::user()->pelanggan;

        if (! $pelanggan || ! $pelanggan->isVerified()) {
            return back()->with('error', 'Anda harus terverifikasi terlebih dahulu untuk melakukan pemesanan. Pastikan dokumen Anda sudah diunggah dan diverifikasi admin.');
        }

        if ($pelanggan->isBlacklisted()) {
            return back()->with('error', 'Akun Anda diblokir. Hubungi admin untuk informasi lebih lanjut.');
        }

        if (! $kendaraan->isTersedia()) {
            return back()->with('error', 'Kendaraan tidak tersedia saat ini.');
        }

        return view('pelanggan.booking.create', compact('kendaraan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $pelanggan = Auth::user()->pelanggan;

        if (! $pelanggan || ! $pelanggan->isVerified()) {
            return back()->with('error', 'Akun belum terverifikasi.');
        }

        if ($pelanggan->isBlacklisted()) {
            return back()->with('error', 'Akun Anda diblokir.');
        }

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        if (! $kendaraan->isTersedia()) {
            return back()->with('error', 'Kendaraan tidak tersedia.');
        }

        // Hitung durasi dan estimasi biaya
        $mulai   = \Carbon\Carbon::parse($request->tanggal_mulai);
        $selesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $durasi  = $mulai->diffInDays($selesai);
        $estimasi = $durasi * $kendaraan->tarif_harian;

        // Menggunakan database transaction untuk keamanan data terintegrasi
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Buat data booking
            $booking = Booking::create([
                'kode_booking'    => 'BKG-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'pelanggan_id'    => $pelanggan->id,
                'kendaraan_id'    => $request->kendaraan_id,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'durasi_hari'     => $durasi,
                'estimasi_biaya'  => $estimasi,
                'catatan'         => $request->catatan,
                'status'          => 'pending',
            ]);

            // 2. Kirim Notifikasi Sistem otomatis ke user tersebut
            // Format menggunakan [BOOKING] agar dibaca dinamis oleh view template sebelumnya
            $judulNotif = '[BOOKING] Pengajuan Sewa Kendaraan ' . $booking->kode_booking;
            $pesanNotif = "Halo " . Auth::user()->name . ", pengajuan booking untuk mobil " . $kendaraan->nama . " (" . $durasi . " hari) dengan estimasi biaya Rp " . number_format($estimasi, 0, ',', '.') . " berhasil dibuat. Silakan tunggu verifikasi tim admin.";

            \Illuminate\Support\Facades\DB::table('notifikasi')->insert([
                'user_id'    => Auth::id(), // ID dari tabel users (bukan pelanggan_id)
                'judul'      => $judulNotif,
                'pesan'      => $pesanNotif,
                'url'        => route('pelanggan.booking.show', $booking->id), // Klik notif langsung diarahkan ke detail booking ini
                'read_at'    => null,
                'created_at' => now(), // Mengikuti jam Asia/Jakarta
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('pelanggan.booking.index')
                ->with('success', 'Booking berhasil dibuat! Menunggu persetujuan admin.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function show(Booking $booking)
    {
        // Pastikan hanya pemilik yang bisa lihat
        if ($booking->pelanggan->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['kendaraan.kategori', 'transaksiSewa.pembayaran.metodePembayaran']);

        return view('pelanggan.booking.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->pelanggan->user_id !== Auth::id()) {
            abort(403);
        }

        if (! in_array($booking->status, ['pending'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan.');
        }

        $booking->update(['status' => 'dibatalkan']);

        return redirect()->route('pelanggan.booking.index')
            ->with('success', 'Booking berhasil dibatalkan.');
    }
}
