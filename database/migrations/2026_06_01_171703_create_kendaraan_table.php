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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                ->constrained('kategori_kendaraan')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('nama', 100);
            $table->string('merk', 100);
            $table->string('model', 100);
            $table->year('tahun');
            $table->string('plat_nomor', 20)->unique();
            $table->string('warna', 50)->nullable();
            $table->unsignedTinyInteger('kapasitas')->default(4);
            $table->enum('transmisi', ['manual', 'matic'])->default('manual');
            $table->enum('bahan_bakar', ['bensin', 'diesel', 'listrik'])->default('bensin');
            $table->decimal('tarif_harian', 12, 2)->default(0.00);
            $table->decimal('denda_per_jam', 12, 2)->default(0.00);
            $table->enum('status', ['aktif', 'non-aktif','disewa', 'servis'])->default('aktif');
            $table->string('foto', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->index('kategori_id', 'idx_kendaraan_kategori');
            $table->index('status', 'idx_kendaraan_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
