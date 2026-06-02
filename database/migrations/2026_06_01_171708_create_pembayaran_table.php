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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')
                ->constrained('transaksi_sewa')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('metode_pembayaran_id')
                ->constrained('metode_pembayaran')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('jumlah_bayar', 12, 2)->default(0.00);
            $table->decimal('jumlah_kembali', 12, 2)->default(0.00);
            $table->string('bukti_transfer', 255)->nullable();
            $table->enum('status', ['pending', 'lunas', 'gagal'])->default('pending');
            $table->timestamps();

            $table->index('transaksi_id', 'idx_pembayaran_transaksi');
            $table->index('metode_pembayaran_id', 'idx_pembayaran_metode');
            $table->index('status', 'idx_pembayaran_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
