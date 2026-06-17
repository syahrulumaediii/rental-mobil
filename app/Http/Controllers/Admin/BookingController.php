<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['pelanggan.user', 'kendaraan', 'disetujuiOleh', 'dibuatOleh']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode_booking', 'like', "%{$request->search}%")
                ->orWhereHas('pelanggan.user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $booking = $query->latest()->paginate(15);

        return view('admin.booking.index', compact('booking'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['pelanggan.user', 'pelanggan.dokumen', 'kendaraan.kategori', 'disetujuiOleh', 'dibuatOleh', 'transaksiSewa']);

        return view('admin.booking.show', compact('booking'));
    }

    public function disetujui(Request $request, Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses.');
        }

        if ($booking->pelanggan->isBlacklisted()) {
            return back()->with('error', 'Pelanggan ini ada dalam daftar blacklist.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Update status booking & input data deposit dari form admin
            $booking->update([
                'status'         => 'disetujui',
                'disetujui_oleh' => Auth::id(),
                'disetujui_at'   => now(),
                'deposit'        => $request->nominal_deposit ?? 0,
                'is_deposit'     => $request->pilih_deposit == 1,
            ]);

            // 2. Ubah status kendaraan menjadi tidak tersedia
            $booking->kendaraan->update(['status' => 'disewa']);

            // 3. Ambil data User Pelanggan untuk menyebutkan nama di notifikasi
            $userPelanggan = $booking->pelanggan->user;

            // Buat kalimat tambahan jika ada deposit
            $teksDeposit = $request->pilih_deposit == 1 
                ? " serta bersiap membayar jaminan deposit sebesar Rp " . number_format($request->nominal_deposit, 0, ',', '.') 
                : "";

            // 4. Kirim Notifikasi Sukses ke Pelanggan
            $judulNotif = '[BOOKING] Pengajuan Sewa Disetujui';
            $pesanNotif = "Halo " . $userPelanggan->name . ", kabar baik! Pengajuan sewa kendaraan Anda dengan kode *" . $booking->kode_booking . "* untuk unit *" . $booking->kendaraan->nama . "* telah DISETUJUI oleh Admin. Silakan datang ke kantor sesuai tanggal mulai sewa untuk proses serah terima unit" . $teksDeposit . ". Terima kasih.";

            \Illuminate\Support\Facades\DB::table('notifikasi')->insert([
                'user_id'    => $userPelanggan->id, // ID User tujuan
                'judul'      => $judulNotif,
                'pesan'      => $pesanNotif,
                'url'        => route('pelanggan.booking.show', $booking->id), // Klik notif langsung ke detail booking pelanggan
                'read_at'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Booking berhasil disetujui dan notifikasi telah dikirim ke pelanggan.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    public function ditolak(Request $request, Booking $booking)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Update status booking menjadi ditolak beserta alasannya
            $booking->update([
                'status'            => 'ditolak',
                'alasan_penolakan'  => $request->alasan_penolakan,
                'disetujui_oleh' => Auth::id(),
                'disetujui_at'   => now(),
            ]);

            // 2. Ambil data User Pelanggan
            $userPelanggan = $booking->pelanggan->user;

            // 3. Kirim Notifikasi Penolakan ke Pelanggan
            $judulNotif = '[BOOKING] Pengajuan Sewa Ditolak';
            $pesanNotif = "Mohon maaf " . $userPelanggan->name . ", pengajuan sewa kendaraan Anda dengan kode *" . $booking->kode_booking . "* (" . $booking->kendaraan->nama . ") DITOLAK oleh Admin. Alasan Penolakan: \"" . $request->alasan_penolakan . "\". Silakan cek kembali data Anda atau lakukan pengajuan ulang menggunakan unit kendaraan lain.";

            \Illuminate\Support\Facades\DB::table('notifikasi')->insert([
                'user_id'    => $userPelanggan->id,
                'judul'      => $judulNotif,
                'pesan'      => $pesanNotif,
                'url'        => route('pelanggan.booking.show', $booking->id),
                'read_at'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Booking berhasil ditolak dan alasan telah diinfokan ke pelanggan.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menolak booking: ' . $e->getMessage());
        }
    }
}
