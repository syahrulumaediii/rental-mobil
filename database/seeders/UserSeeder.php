<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Seed users
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        User::create([
            'name' => 'Kasir Utama',
            'email' => 'kasir@gmail.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir',
            'phone' => '081234567891',
        ]);

        User::create([
            'name' => 'SYAHRUL UMAEDI',
            'email' => 'pelanggan@gmail.com',
            'password' => Hash::make('pelanggan123'),
            'role' => 'pelanggan',
            'phone' => '081234567892',
        ]);
    }
}
