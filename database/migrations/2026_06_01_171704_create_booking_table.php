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
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking', 50)->unique();
            $table->foreignId('pelanggan_id')
                ->constrained('pelanggan')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('kendaraan_id')
                ->constrained('kendaraan')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->unsignedInteger('durasi_hari')->default(1);
            $table->decimal('estimasi_biaya', 12, 2)->default(0.00);
            $table->text('catatan')->nullable();
            $table->enum('status', [
                'pending',
                'disetujui',
                'ditolak',
                'aktif',
                'selesai',
                'dibatalkan',
            ])->default('pending');
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('disetujui_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamp('disetujui_oleh')->nullable();
            $table->timestamps();

            $table->index('pelanggan_id', 'idx_booking_pelanggan');
            $table->index('kendaraan_id', 'idx_booking_kendaraan');
            $table->index('status', 'idx_booking_status');
            $table->index(['tanggal_mulai', 'tanggal_selesai'], 'idx_booking_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
