<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MetodePembayaran;

class MetodePembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        MetodePembayaran::create([
            'nama' => 'Cash',
            'tipe' => 'tunai',
            'is_active' => true,
        ]);

        MetodePembayaran::create([
            'nama' => 'Transfer Bank',
            'tipe' => 'transfer',
            'is_active' => true,
        ]);

        MetodePembayaran::create([
            'nama' => 'DANA',
            'tipe' => 'ewallet',
            'is_active' => true,
        ]);

        MetodePembayaran::create([
            'nama' => 'QRIS',
            'tipe' => 'qris',
            'is_active' => true,
        ]);
    }
}
