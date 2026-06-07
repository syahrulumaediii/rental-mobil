<?php

namespace App\Notifications;

use App\Models\TransaksiSewa;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SerahTerimaNotification extends Notification
{
    use Queueable;

    protected $transaksi;
    protected $target;

    public function __construct(TransaksiSewa $transaksi, $target = 'pelanggan')
    {
        $this->transaksi = $transaksi;
        $this->target = $target;
    }

    public function via($notifiable)
    {
        return ['database']; // Menyimpan ke tabel notifications database
    }

    public function toArray($notifiable)
    {
        $mobil = $this->transaksi->booking->kendaraan->nama;
        $plat  = $this->transaksi->booking->kendaraan->plat_nomor;
        $biaya = number_format($this->transaksi->total_biaya, 0, ',', '.');
        $trx   = $this->transaksi->kode_transaksi;

        if ($this->target === 'pelanggan') {
            return [
                'title' => 'Unit Kendaraan Siap Dikendarai! 🚗',
                'message' => "Halo {$notifiable->name}, proses serah terima unit {$mobil} ({$plat}) telah selesai dilakukan. Pembayaran sewa sebesar Rp {$biaya} telah kami terima dengan status LUNAS. Selamat menikmati perjalanan Anda dan utamakan keselamatan berlalulintas!",
                'transaksi_id' => $this->transaksi->id,
                'type' => 'serah_terima_pelanggan'
            ];
        }

        // Teks untuk Staff (Admin/Kasir)
        return [
            'title' => 'Pembayaran Masuk: Serah Terima Berhasil 💰',
            'message' => "Kas Masuk! Pembayaran sewa di awal untuk kode transaksi {$trx} (Unit: {$mobil}) sebesar Rp {$biaya} telah berhasil divalidasi dan masuk ke sistem keuangan.",
            'transaksi_id' => $this->transaksi->id,
            'type' => 'serah_terima_staff'
        ];
    }
}