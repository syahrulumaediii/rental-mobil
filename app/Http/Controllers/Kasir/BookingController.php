<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;


class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['pelanggan.user', 'kendaraan', 'disetujuiOleh']);

        // Hitung jumlah booking yang statusnya 'pending'
        $pendingCount = Booking::where('status', 'pending')->count();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode_booking', 'like', "%{$request->search}%")
                ->orWhereHas('pelanggan.user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $booking = $query->latest()->paginate(15);

        // Kirim $pendingCount ke view
        return view('kasir.booking.index', compact('booking', 'pendingCount'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['pelanggan.user', 'pelanggan.dokumen', 'kendaraan.kategori', 'disetujuiOleh', 'dibuatOleh', 'transaksiSewa']);

        return view('kasir.booking.show', compact('booking'));
    }

    public function disetujui(Request $request, Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses.');
        }

        if ($booking->pelanggan->isBlacklisted()) {
            return back()->with('error', 'Pelanggan ini ada dalam daftar blacklist.');
        }

        $booking->update([
            'status' => 'disetujui',
            'disetujui_oleh' => Auth::id(),
            'disetujui_at' => now(),
        ]);

        // Ubah status kendaraan menjadi tidak tersedia
        $booking->kendaraan->update(['status' => 'disewa']);

        // 🌟 BARU: Simpan ke tabel notifikasi kustom Anda
        Notifikasi::create([
            'user_id'    => $booking->pelanggan->user_id, // ID Akun pelanggan
            'judul'      => '🎉 Booking Kendaraan Disetujui!',
            'pesan'      => "Pengajuan sewa unit " . $booking->kendaraan->merk . " " . $booking->kendaraan->nama . " (" . $booking->kendaraan->plat_nomor . ") dengan kode booking " . $booking->kode_booking . " telah disetujui. Silakan lakukan pengambilan unit sesuai jadwal.",
            'tipe'       => 'booking', // Sesuai dengan enum di tabel Anda
            'read_at'    => null,
            'url'        => route('pelanggan.booking.show', $booking->id), // Link ke detail booking di sisi pelanggan
        ]);

        return back()->with([
            'success' => 'Booking berhasil disetujui dan notifikasi dikirim ke akun pelanggan.',
            'trigger_wa' => 'disetujui',
            'booking_id' => $booking->id
        ]);
    }

    public function ditolak(Request $request, Booking $booking)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses.');
        }

        $booking->update([
            'status'            => 'ditolak',
            'alasan_penolakan'  => $request->alasan_penolakan,
            'disetujui_oleh' => Auth::id(),
            'disetujui_at'   => now(),
        ]);

        // 🌟 BARU: Simpan ke tabel notifikasi kustom Anda
        Notifikasi::create([
            'user_id'    => $booking->pelanggan->user_id, // ID Akun pelanggan
            'judul'      => '❌ Pengajuan Booking Ditolak',
            'pesan'      => "Mohon maaf, pengajuan sewa dengan kode " . $booking->kode_booking . " ditolak dengan alasan: \"" . $request->alasan_penolakan . "\".",
            'tipe'       => 'booking', // Sesuai dengan enum di tabel Anda
            'read_at'    => null,
            'url'        => route('pelanggan.booking.show', $booking->id),
        ]);

        return back()->with([
            'success' => 'Booking berhasil ditolak dan notifikasi dikirim ke akun pelanggan.',
            'trigger_wa' => 'ditolak',
            'booking_id' => $booking->id
        ]);
    }
}
