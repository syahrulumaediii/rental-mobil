<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendWhatsAppNotification;
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
        $query = Kendaraan::with(['kategori', 'bookings']);

        // Filter kondisi fisik kendaraan
        $query->where('status', '!=', 'rusak');

        // 🌟 BARU: Jika user mengisi filter tanggal pencarian sewa
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $tglMulai = \Carbon\Carbon::parse($request->tgl_mulai);
            $tglSelesai = \Carbon\Carbon::parse($request->tgl_selesai);

            // Sembunyikan kendaraan yang sudah ditabrak/tumpang tindih jadwalnya di tanggal tersebut
            $query->whereDoesntHave('bookings', function ($q) use ($tglMulai, $tglSelesai) {
                $q->whereIn('status', ['disetujui', 'aktif'])
                    ->where(function ($queryTabrakan) use ($tglMulai, $tglSelesai) {
                        $queryTabrakan->whereBetween('tanggal_mulai', [$tglMulai, $tglSelesai])
                            ->orWhereBetween('tanggal_selesai', [$tglMulai, $tglSelesai])
                            ->orWhere(function ($qEkstrim) use ($tglMulai, $tglSelesai) {
                                $qEkstrim->where('tanggal_mulai', '<=', $tglMulai)
                                    ->where('tanggal_selesai', '>=', $tglSelesai);
                            });
                    });
            });
        } else {
            // Jika user tidak memfilter tanggal, defaultnya gunakan sewa yang sedang berjalan saat ini (now)
            $query->whereDoesntHave('bookings', function ($q) {
                $q->whereIn('status', ['pending', 'disetujui', 'aktif'])
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_selesai', '>=', now());
            });
        }

        // Filter Kategori, Transmisi, dan Tarif tetap sama
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
            return back()->with('error', 'Anda harus terverifikasi terlebih dahulu.');
        }

        if ($pelanggan->isBlacklisted()) {
            return back()->with('error', 'Akun Anda diblokir.');
        }

        if (in_array($kendaraan->status, ['rusak', 'servis'])) {
            return back()->with('error', 'Kendaraan sedang dalam perbaikan.');
        }

        // 🌟 KUNCI: Set waktu dari JAM 00:00 di tanggal mulai sampai JAM 23:59 di tanggal selesai
        $bookingTerjadwal = $kendaraan->bookings()
            ->whereIn('status', ['disetujui', 'aktif'])
            ->get(['tanggal_mulai', 'tanggal_selesai'])
            ->map(function ($b) {
                return [
                    'from' => \Carbon\Carbon::parse($b->tanggal_mulai)->format('Y-m-d H:i'),
                    'to'   => \Carbon\Carbon::parse($b->tanggal_selesai)->format('Y-m-d H:i')
                ];
            });

        return view('pelanggan.booking.create', compact('kendaraan', 'bookingTerjadwal'));
    }

    public function store(Request $request)
    {
        // 1. Validasi diperbarui ke format d-m-Y H:i
        $request->validate([
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_mulai'   => 'required|date_format:d-m-Y H:i',
            'tanggal_selesai' => 'required|date_format:d-m-Y H:i|after:tanggal_mulai',
            'catatan'         => 'nullable|string|max:500',
        ]);

        try {
            // 2. Konversi format Indonesia (d-m-Y H:i) ke format Database (Y-m-d H:i:s)
            $mulai   = \Carbon\Carbon::createFromFormat('d-m-Y H:i', $request->tanggal_mulai);
            $selesai = \Carbon\Carbon::createFromFormat('d-m-Y H:i', $request->tanggal_selesai);

            $dbTanggalMulai   = $mulai->format('Y-m-d H:i:s');
            $dbTanggalSelesai = $selesai->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return back()->with('error', 'Format tanggal dan waktu tidak valid.');
        }

        // 3. Ambil data pelanggan yang sedang login
        $user = Auth::user();
        $pelanggan = $user->pelanggan;

        // 4. Pengecekan standar keamanan khusus pelanggan
        if (!$pelanggan) {
            return back()->with('error', 'Profil pelanggan tidak ditemukan.');
        }
        if (!$pelanggan->isVerified()) {
            return back()->with('error', 'Akun Anda belum terverifikasi. Silakan lengkapi dokumen.');
        }
        if ($pelanggan->isBlacklisted()) {
            return back()->with('error', 'Akun Anda diblokir.');
        }

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        // 🌟 VALIDASI REAL-TIME BERDASARKAN TANGGAL PILIHAN USER
        $isJadwalTabrakan = \App\Models\Booking::where('kendaraan_id', $kendaraan->id)
            ->whereIn('status', ['disetujui', 'aktif']) // Hanya memblokir jika statusnya sudah disetujui atau aktif berjalan
            ->where(function ($queryTabrakan) use ($dbTanggalMulai, $dbTanggalSelesai) {
                $queryTabrakan->whereBetween('tanggal_mulai', [$dbTanggalMulai, $dbTanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$dbTanggalMulai, $dbTanggalSelesai])
                    ->orWhere(function ($qEkstrim) use ($dbTanggalMulai, $dbTanggalSelesai) {
                        $qEkstrim->where('tanggal_mulai', '<=', $dbTanggalMulai)
                            ->where('tanggal_selesai', '>=', $dbTanggalSelesai);
                    });
            })->exists();

        // Perbarui pesan error agar lebih informatif sesuai filter tanggal
        if ($isJadwalTabrakan) {
            return back()->with('error', 'Kendaraan tidak tersedia pada tanggal dan waktu yang Anda pilih.');
        }

        // 5. Hitung durasi (Menggunakan pembulatan hari ke atas jika lewat 1 jam dari durasi harian)
        $diffInHours = $mulai->diffInHours($selesai);
        $durasi = ceil($diffInHours / 24);
        if ($durasi <= 0) $durasi = 1; // Minimal sewa adalah 1 hari

        $estimasi = $durasi * $kendaraan->tarif_harian;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 6. Simpan Booking dengan nilai tanggal + waktu terbaru
            $booking = Booking::create([
                'kode_booking'    => 'BKG-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'pelanggan_id'    => $pelanggan->id,
                'kendaraan_id'    => $request->kendaraan_id,
                'tanggal_mulai'   => $dbTanggalMulai,
                'tanggal_selesai' => $dbTanggalSelesai,
                'durasi_hari'     => $durasi,
                'estimasi_biaya'  => $estimasi,
                'catatan'         => $request->catatan,
                'status'          => 'pending',
                'sumber_booking'  => 'online',
                'dibuat_oleh'     => $user->id,
            ]);

            // 7. Simpan Riwayat Notifikasi
            \Illuminate\Support\Facades\DB::table('notifikasi')->insert([
                'user_id'    => $user->id,
                'judul'      => '[BOOKING] Pengajuan Sewa Kendaraan ' . $booking->kode_booking,
                'pesan'      => "Halo " . Auth::user()->name . ", pengajuan booking untuk mobil " . $kendaraan->nama . " (" . $durasi . " hari) dengan estimasi biaya Rp " . number_format($estimasi, 0, ',', '.') . " berhasil dibuat. Silakan tunggu verifikasi tim admin.",
                'tipe'       => 'booking',
                'url'        => route('pelanggan.booking.show', $booking->id),
                'read_at'    => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $pesanWa = "Halo " . $user->name . ", pengajuan booking " . $booking->kode_booking . " berhasil dibuat. Mohon tunggu verifikasi admin.";
            SendWhatsAppNotification::dispatch($user, $pesanWa);
            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('pelanggan.booking.index')
                ->with('success', 'Booking berhasil diajukan!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
