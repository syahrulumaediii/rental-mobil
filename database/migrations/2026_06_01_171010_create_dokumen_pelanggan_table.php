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
        Schema::create('dokumen_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')
                ->constrained('pelanggan')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('jenis_dokumen', [
                'ktp',
                'sim',
                'paspor',
                'lainnya',
            ]);
            $table->string('file_path', 255);
            $table->enum('status', [
                'pending',
                'verified',
                'rejected',
            ])->default('pending');
            $table->text('catatan')->nullable();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index('pelanggan_id', 'idx_dokumen_pelanggan_id');
            $table->index('status', 'idx_dokumen_status');
            $table->index('jenis_dokumen', 'idx_dokumen_jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pelanggan');
    }
};
