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
        Schema::create('denda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')
                ->constrained('transaksi_sewa')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->enum('jenis_denda', ['keterlambatan', 'kerusakan', 'kehilangan']);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('jumlah_hari_telat')->nullable()->default(0);
            $table->decimal('tarif_denda', 12, 2)->default(0.00);
            $table->decimal('total_denda', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index('transaksi_id', 'idx_denda_transaksi');
            $table->index('jenis_denda', 'idx_denda_jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denda');
    }
};
