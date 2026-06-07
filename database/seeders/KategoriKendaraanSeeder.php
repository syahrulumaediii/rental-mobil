<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KategoriKendaraan;

class KategoriKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        KategoriKendaraan::create([
            'nama' => 'Mobil',
            'deskripsi' => 'Kategori kendaraan mobil penumpang',
        ]);

        KategoriKendaraan::create([
            'nama' => 'SUV',
            'deskripsi' => 'Mobil sport utility vehicle',
        ]);

        KategoriKendaraan::create([
            'nama' => 'City Car',
            'deskripsi' => 'Mobil kecil untuk penggunaan perkotaan',
        ]);

        KategoriKendaraan::create([
            'nama' => 'Sedan',
            'deskripsi' => 'Mobil sedan untuk kenyamanan berkendara',
        ]);

        KategoriKendaraan::create([
            'nama' => 'Premium',
            'deskripsi' => 'Mobil premium dan eksekutif',
        ]);
    }
}
