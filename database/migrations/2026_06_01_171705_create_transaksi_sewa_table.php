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
        Schema::create('transaksi_sewa', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->foreignId('booking_id')
                ->unique()
                ->constrained('booking')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('kasir_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('tanggal_ambil_aktual');
            $table->dateTime('tanggal_kembali_aktual')->nullable();
            $table->decimal('total_biaya', 12, 2)->default(0.00);
            $table->decimal('total_denda', 12, 2)->default(0.00);
            $table->decimal('total_bayar', 12, 2)->default(0.00);
            $table->enum('status', ['berjalan', 'selesai'])->default('berjalan');
            $table->text('catatan_kasir')->nullable();
            $table->timestamps();

            $table->index('kasir_id', 'idx_transaksi_kasir');
            $table->index('status', 'idx_transaksi_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_sewa');
    }
};
