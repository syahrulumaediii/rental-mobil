<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\KategoriKendaraan;
use App\Models\MerekKendaraan;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KategoriKendaraanSeeder::class,
            KendaraanSeeder::class,
            MetodePembayaranSeeder::class,
        ]);
    }
}
