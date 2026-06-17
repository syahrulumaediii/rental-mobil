<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriKendaraan;
use App\Models\Kendaraan;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengambil kategori pertama yang aktif
        $kategori = KategoriKendaraan::first();

        // Jika belum ada kategori sama sekali di database, buat cadangan agar seeder tidak error
        if (!$kategori) {
            $kategori = KategoriKendaraan::create(['nama' => 'Mobil']);
        }

        Kendaraan::create([
            'kategori_id'   => $kategori->id,
            'nama'          => 'Avanza 2022',
            'merk'          => 'Toyota',
            'model'         => 'Avanza',
            'tahun'         => 2022,
            'plat_nomor'    => 'B 1234 ABC',
            'warna'         => 'Putih',
            'kapasitas'     => 7,
            'transmisi'     => 'manual',
            'bahan_bakar'   => 'bensin',
            'tarif_harian'  => 350000,
            'denda_per_jam' => 35000, // 🌟 Tambahan kolom denda
            'status'        => 'aktif',
            'foto'          => null,
            'deskripsi'     => 'Mobil keluarga hemat BBM',
        ]);

        Kendaraan::create([
            'kategori_id'   => $kategori->id,
            'nama'          => 'Toyota Rush 2023',
            'merk'          => 'Toyota',
            'model'         => 'Rush',
            'tahun'         => 2023,
            'plat_nomor'    => 'B 2345 DEF',
            'warna'         => 'Hitam',
            'kapasitas'     => 7,
            'transmisi'     => 'manual',
            'bahan_bakar'   => 'bensin',
            'tarif_harian'  => 400000,
            'denda_per_jam' => 40000, // 🌟 Tambahan kolom denda
            'status'        => 'aktif',
            'foto'          => null,
            'deskripsi'     => 'SUV keluarga nyaman untuk perjalanan jauh',
        ]);

        Kendaraan::create([
            'kategori_id'   => $kategori->id,
            'nama'          => 'Honda Brio 2022',
            'merk'          => 'Honda',
            'model'         => 'Brio',
            'tahun'         => 2022,
            'plat_nomor'    => 'B 3456 GHI',
            'warna'         => 'Merah',
            'kapasitas'     => 5,
            'transmisi'     => 'matic',
            'bahan_bakar'   => 'bensin',
            'tarif_harian'  => 300000,
            'denda_per_jam' => 30000, // 🌟 Tambahan kolom denda
            'status'        => 'aktif',
            'foto'          => null,
            'deskripsi'     => 'City car irit dan lincah',
        ]);

        Kendaraan::create([
            'kategori_id'   => $kategori->id,
            'nama'          => 'Mitsubishi Xpander 2024',
            'merk'          => 'Mitsubishi',
            'model'         => 'Xpander',
            'tahun'         => 2024,
            'plat_nomor'    => 'B 4567 JKL',
            'warna'         => 'Silver',
            'kapasitas'     => 7,
            'transmisi'     => 'matic',
            'bahan_bakar'   => 'bensin',
            'tarif_harian'  => 450000,
            'denda_per_jam' => 45000, // 🌟 Tambahan kolom denda
            'status'        => 'aktif',
            'foto'          => null,
            'deskripsi'     => 'MPV modern dengan kabin luas',
        ]);

        Kendaraan::create([
            'kategori_id'   => $kategori->id,
            'nama'          => 'Daihatsu Sigra 2021',
            'merk'          => 'Daihatsu',
            'model'         => 'Sigra',
            'tahun'         => 2021,
            'plat_nomor'    => 'B 5678 MNO',
            'warna'         => 'Putih',
            'kapasitas'     => 7,
            'transmisi'     => 'manual',
            'bahan_bakar'   => 'bensin',
            'tarif_harian'  => 275000,
            'denda_per_jam' => 25000, // 🌟 Tambahan kolom denda
            'status'        => 'aktif',
            'foto'          => null,
            'deskripsi'     => 'Mobil keluarga ekonomis',
        ]);

        Kendaraan::create([
            'kategori_id'   => $kategori->id,
            'nama'          => 'Suzuki Ertiga 2023',
            'merk'          => 'Suzuki',
            'model'         => 'Ertiga',
            'tahun'         => 2023,
            'plat_nomor'    => 'B 6789 PQR',
            'warna'         => 'Abu-abu',
            'kapasitas'     => 7,
            'transmisi'     => 'matic',
            'bahan_bakar'   => 'bensin',
            'tarif_harian'  => 375000,
            'denda_per_jam' => 35000, // 🌟 Tambahan kolom denda
            'status'        => 'aktif',
            'foto'          => null,
            'deskripsi'     => 'MPV nyaman dan hemat bahan bakar',
        ]);
    }
}
