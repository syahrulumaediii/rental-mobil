<?php

namespace App\Notifications;

use App\Models\TransaksiSewa;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengembalianNotification extends Notification
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
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $mobil = $this->transaksi->booking->kendaraan->nama;
        $plat  = $this->transaksi->booking->kendaraan->plat_nomor;
        $denda = $this->transaksi->total_denda;
        $trx   = $this->transaksi->kode_transaksi;

        if ($this->target === 'pelanggan') {
            if ($denda > 0) {
                $nominalDenda = number_format($denda, 0, ',', '.');
                return [
                    'title' => 'Pengembalian Selesai (Catatan Denda) ⚠️',
                    'message' => "Terima kasih telah mengembalikan unit {$mobil} ({$plat}). Berdasarkan pengecekan fisik/waktu aktual, terdapat rekapitulasi denda sebesar Rp {$nominalDenda}. Potongan telah disesuaikan melalui saldo jaminan/pembayaran di tempat. Terima kasih atas kerja samanya.",
                    'transaksi_id' => $this->transaksi->id,
                    'type' => 'pengembalian_denda_pelanggan'
                ];
            }

            return [
                'title' => 'Terima Kasih, Transaksi Selesai! ✨',
                'message' => "Unit {$mobil} ({$plat}) telah sukses diterima kembali dalam kondisi baik. Uang deposit jaminan Anda dikembalikan sepenuhnya. Terima kasih telah mempercayai layanan kami, sampai jumpa di sewa berikutnya!",
                'transaksi_id' => $this->transaksi->id,
                'type' => 'pengembalian_clear_pelanggan'
            ];
        }

        // Teks untuk Staff (Admin/Kasir)
        $nominalDendaStaff = number_format($denda, 0, ',', '.');
        $pesanStaff = $denda > 0 
            ? "Pengembalian {$trx} selesai dengan denda sebesar Rp {$nominalDendaStaff}. Dana jaminan deposit/tambahan kas tunai telah berhasil dibukukan."
            : "Pengembalian {$trx} bersih tanpa denda. Unit aman dan jaminan deposit dikembalikan utuh ke pelanggan.";

        return [
            'title' => 'Unit Kembali: Sesi Sewa Ditutup ✅',
            'message' => $pesanStaff,
            'transaksi_id' => $this->transaksi->id,
            'type' => 'pengembalian_staff'
        ];
    }
}