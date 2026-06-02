<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kondisi_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')
                ->constrained('transaksi_sewa')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->enum('waktu', ['sebelum', 'sesudah']);
            $table->string('foto', 255)->nullable();
            $table->enum('bahan_bakar', ['penuh', '3/4', '1/2', '1/4', 'kosong'])->default('penuh');
            $table->unsignedInteger('km_odometer')->default(0);
            $table->text('catatan_kondisi')->nullable();
            $table->foreignId('dicatat_oleh')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();

            $table->index('transaksi_id', 'idx_kondisi_transaksi');
            $table->index('waktu', 'idx_kondisi_waktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kondisi_kendaraan');
    }
};
