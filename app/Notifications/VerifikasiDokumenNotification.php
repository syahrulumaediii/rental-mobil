<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;

class VerifikasiDokumenNotification extends Notification
{
    use Queueable;

    protected $dokumen;
    protected $statusAkun;

    public function __construct($dokumen, $statusAkun)
    {
        $this->dokumen = $dokumen;
        $this->statusAkun = $statusAkun;
    }

    /**
     * Method kustom untuk menyimpan langsung ke tabel 'notifikasi' Anda
     */
    public function sendCustomNotification(User $user)
    {
        $jenisDokumen = strtoupper($this->dokumen->jenis_dokumen);

        // Default Judul & Pesan
        $judul = "Pembaruan Verifikasi Dokumen";
        $pesan = "Dokumen {$jenisDokumen} Anda sedang diperiksa.";
        $url = route('pelanggan.profile'); // Sesuaikan dengan route halaman profile/notifikasi pelanggan

        // Percabangan logika pesan berdasarkan status approval dokumen & akun
        if ($this->statusAkun === 'verified') {
            $judul = "🎉 Akun Anda Telah Aktif!";
            $pesan = "Selamat! Dokumen {$jenisDokumen} disetujui. Seluruh dokumen Anda telah diverifikasi, sekarang Anda sudah bisa melakukan booking kendaraan.";
        } elseif ($this->dokumen->status === 'verified') {
            $judul = "✅ Dokumen {$jenisDokumen} Disetujui";
            $pesan = "Dokumen {$jenisDokumen} Anda berhasil diverifikasi. Menunggu dokumen wajib lainnya selesai diverifikasi.";
        } elseif ($this->dokumen->status === 'rejected') {
            $judul = "❌ Dokumen {$jenisDokumen} Ditolak";
            $pesan = "Dokumen {$jenisDokumen} Anda ditolak karena: " . ($this->dokumen->catatan ?? 'Data tidak sesuai.') . ". Silakan unggah kembali dokumen yang valid.";
        }

        // Jalankan query insert ke relasi 'notifikasi' milik model User
        return $user->notifikasi()->create([
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe'  => $this->statusAkun === 'verified' ? 'success' : ($this->dokumen->status === 'rejected' ? 'danger' : 'info'),
            'url'   => $url,
            'is_read' => false,
            // sertakan kolom lain jika tabel 'notifikasi' Anda memilikinya
        ]);
    }

    // Mengosongkan via array/database bawaan laravel jika Anda hanya memakai tabel kustom
    public function via($notifiable)
    {
        return [];
    }
}
