<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlacklistPelanggan;
use App\Models\DokumenPelanggan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with('user');

        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
                ->orWhere('nik', 'like', "%{$request->search}%");
        }

        $pelanggan = $query->latest()->paginate(15);

        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load(['user', 'dokumen.verifier', 'booking.kendaraan', 'blacklist.admin']);

        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    // ===================== VERIFIKASI DOKUMEN =====================

    public function verifikasiDokumen(Request $request, DokumenPelanggan $dokumen)
    {
        $request->validate([
            'status'  => 'required|in:verified,rejected',
            'catatan' => 'nullable|string|max:500',
        ]);

        $dokumen->update([
            'status'      => $request->status,
            'catatan'     => $request->catatan,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Ambil data pelanggan beserta relasi user-nya
        $pelanggan = Pelanggan::with(['dokumen', 'user'])->find($dokumen->pelanggan_id);

        $semuaVerified = $pelanggan->dokumen()
            ->whereIn('jenis_dokumen', ['ktp', 'sim'])
            ->where('status', 'verified')
            ->count() === 2;

        if ($semuaVerified) {
            $pelanggan->update(['status_verifikasi' => 'verified']);
            $pelanggan->refresh(); // Refresh agar status terbaru masuk ke memori
        } else {
            if ($request->status === 'rejected') {
                $pelanggan->update(['status_verifikasi' => 'rejected']);
                $pelanggan->refresh();
            }
        }

        // ==================== OTOMATIS KIRIM NOTIFIKASI BAGUS ====================
        // ==================== OTOMATIS KIRIM NOTIFIKASI EKSKLUSIF ====================
        if ($pelanggan && $pelanggan->user) {
            $jenisDokumen = strtoupper($dokumen->jenis_dokumen);
            $namaPelanggan = $pelanggan->user->name;

            // Inisialisasi variabel agar tidak kosong
            $judul = null;
            $pesan = null;

            // LOGIKA 1: JIKA AKUN SELESAI DIVERIFIKASI (KTP & SIM APPROVED)
            if ($pelanggan->status_verifikasi === 'verified') {
                $judul = "🎉 Aktivasi Akun Berhasil - Selamat Bergabung!";
                $pesan = "Halo {$namaPelanggan},\n\n" .
                    "Kabar gembira! Tim verifikator kami telah selesai memeriksa seluruh dokumen wajib Anda. " .
                    "Dengan ini, akun Anda dinyatakan VALID dan telah **Aktif Sepenuhnya**.\n\n" .
                    "Sekarang Anda memiliki akses penuh untuk melakukan pemesanan (booking) armada kendaraan " .
                    "terbaik kami kapan saja. Terima kasih telah memercayakan perjalanan Anda bersama kami. " .
                    "Semoga hari Anda menyenangkan dan selamat berkendara dengan aman!";

                // LOGIKA 2: JIKA ADA DOKUMEN YANG DITOLAK (PERLU PERBAIKAN)
            } elseif ($dokumen->status === 'rejected') {
                $judul = "❌ Tindakan Diperlukan: Verifikasi Dokumen {$jenisDokumen} Ditolak";
                $alasan = $request->catatan ? $request->catatan : "Kualitas foto kurang jelas, blur, atau dokumen sudah kedaluwarsa.";

                $pesan = "Halo {$namaPelanggan},\n\n" .
                    "Terima kasih telah melakukan unggah dokumen. Mohon maaf, setelah dilakukan pemeriksaan, " .
                    "dokumen **{$jenisDokumen}** Anda belum dapat kami setujui dengan alasan:\n" .
                    "\"{$alasan}\"\n\n" .
                    "Mohon kesediaannya untuk mengunggah kembali foto dokumen {$jenisDokumen} Anda yang terbaru, " .
                    "asli, dan terlihat jelas tanpa terpotong melalui halaman Profil Akun Anda. " .
                    "Proses verifikasi akan langsung kami lanjutkan setelah dokumen perbaikan kami terima. Terima kasih.";
            }

            // KONTROL: Hanya simpan ke database jika salah satu kondisi di atas terpenuhi
            if ($judul && $pesan) {
                $pelanggan->user->notifikasi()->create([
                    'user_id'    => $pelanggan->user_id,
                    'judul'      => $judul,
                    'pesan'      => $pesan,
                    'tipe'       => 'dokumen', // Sesuai dengan batasan enum di database Anda
                    'url'        => route('admin.pelanggan.show', $pelanggan->id), // Sesuaikan ke arah halaman profil user jika diperlukan
                    'read_at'    => null,
                ]);
            }
        }
        // =========================================================================
        // =========================================================================

        return back()->with('success', 'Dokumen berhasil diperbarui dan notifikasi telah dikirim ke pelanggan.');
    }

    // ===================== BLACKLIST =====================

    public function blacklist(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        // Nonaktifkan blacklist lama jika ada
        BlacklistPelanggan::where('pelanggan_id', $pelanggan->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        BlacklistPelanggan::create([
            'pelanggan_id'     => $pelanggan->id,
            'alasan'           => $request->alasan,
            'ditambahkan_oleh' => Auth::id(),
            'is_active'        => true,
        ]);

        return back()->with('success', 'Pelanggan berhasil dimasukkan ke blacklist.');
    }

    public function unblacklist(Pelanggan $pelanggan)
    {
        BlacklistPelanggan::where('pelanggan_id', $pelanggan->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return back()->with('success', 'Pelanggan berhasil dihapus dari blacklist.');
    }
}
