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
        Schema::create('deposit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')
                ->unique()
                ->constrained('transaksi_sewa')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('jumlah', 12, 2)->default(0.00);
            $table->enum('status', ['ditahan', 'dikembalikan', 'dipotong'])->default('ditahan');
            $table->decimal('jumlah_dipotong', 12, 2)->default(0.00);
            $table->text('alasan_potongan')->nullable();
            $table->timestamp('dikembalikan_at')->nullable();
            $table->timestamps();

            $table->index('status', 'idx_deposit_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposit');
    }
};
