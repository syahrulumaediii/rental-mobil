<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Denda;
use App\Models\Deposit;
use App\Models\KondisiKendaraan;
use App\Models\Pembayaran;
use App\Models\TransaksiSewa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaksiDendaDummySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Buat Data Dummy Booking (Menggunakan Pelanggan ID = 1, Kendaraan ID = 1)
            $booking = Booking::create([
                'id' => 8, // Menggunakan ID 8 agar tidak bentrok dengan data lama
                'kode_booking' => 'BK-20260608-XYZ',
                'pelanggan_id' => 1,
                'kendaraan_id' => 1,
                'tanggal_mulai' => '2026-06-05 09:00:00',
                'tanggal_selesai' => '2026-06-07 09:00:00',
                'estimasi_biaya' => 700000.00,
                'status' => 'selesai',
            ]);

            // 2. Buat Induk Transaksi Sewa
            // Total denda: Rp 250.000 (Keterlambatan 150rb + Kerusakan Bumper 100rb)
            // Total bayar bersih: Rp 950.000 (Sewa Pokok 700rb + Denda 250rb)
            $transaksi = TransaksiSewa::create([
                'id' => 8,
                'kode_transaksi' => 'TRX-DND8829MUT',
                'booking_id' => $booking->id,
                'kasir_id' => 2, // Mengasumsikan ID user kasir yang bertugas
                'tanggal_ambil_aktual' => '2026-06-05 09:15:00',
                'tanggal_kembali_aktual' => '2026-06-07 12:00:00',
                'total_biaya' => 700000.00,
                'total_denda' => 250000.00,
                'total_bayar' => 950000.00,
                'status' => 'selesai',
                'catatan_kasir' => 'Mobil dikembalikan terlat dan ada sedikit goresan di bumper depan kiri.',
            ]);

            // 3. Catat Log Kondisi Fisik Mobil (Sebelum & Sesudah)
            KondisiKendaraan::create([
                'transaksi_id' => $transaksi->id,
                'waktu' => 'sebelum',
                'bahan_bakar' => 'penuh',
                'km_odometer' => 45200,
                'catatan_kondisi' => 'Kondisi mulus bodi bersih, ban serep aman.',
                'dicatat_oleh' => 2,
            ]);

            KondisiKendaraan::create([
                'transaksi_id' => $transaksi->id,
                'waktu' => 'sesudah',
                'bahan_bakar' => 'penuh',
                'km_odometer' => 45450,
                'catatan_kondisi' => 'Bumper depan kiri lecet halus terkena gesekan ranting.',
                'dicatat_oleh' => 2,
            ]);

            // 4. Catat Log Deposit Pelanggan (Status: Dipotong)
            Deposit::create([
                'transaksi_id' => $transaksi->id,
                'jumlah' => 500000.00,
                'status' => 'dipotong',
                'jumlah_dipotong' => 250000.00,
                'alasan_potongan' => 'Dipotong otomatis dari deposit untuk pelunasan denda keterlambatan & kerusakan.',
                'dikembalikan_at' => now(),
            ]);

            // 5. MEMBUAT 2 DATA DUMMY PELANGGARAN DENDA

            // Denda 1: Keterlambatan Pengembalian (3 Jam)
            Denda::create([
                'transaksi_id' => $transaksi->id,
                'jenis_denda' => 'keterlambatan',
                'keterangan' => 'Terlambat mengembalikan unit selama 3 jam dari batas waktu reguler booking',
                'jumlah_jam_telat' => 3,
                'tarif_denda' => 50000.00,
                'total_denda' => 150000.00,
            ]);

            // Denda 2: Kerusakan Fisik Kendaraan (Lecet Bumper)
            Denda::create([
                'transaksi_id' => $transaksi->id,
                'jenis_denda' => 'kerusakan',
                'keterangan' => 'Bumper depan bagian kiri tergores/lecet luar',
                'jumlah_jam_telat' => 0,
                'tarif_denda' => 0.00,
                'total_denda' => 100000.00,
            ]);

            // 6. Catat Histori Kwitansi Pembayaran Pertama (Biaya Pokok Sewa)
            Pembayaran::create([
                'transaksi_id' => $transaksi->id,
                'metode_pembayaran_id' => 1, // Metode tunai/cash
                'jumlah_bayar' => 700000.00,
                'jumlah_kembali' => 0.00,
                'status' => 'lunas',
            ]);
        });
    }
}