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
        //

        $kategori = KategoriKendaraan::first();

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
            'status'        => 'tersedia',
            'foto'          => null,
            'deskripsi'     => 'Mobil keluarga hemat BBM',
        ]);
    }
}
