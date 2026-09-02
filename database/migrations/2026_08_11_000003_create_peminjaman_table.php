<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam', 20)->unique();
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignId('petugas_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->date('tgl_pinjam');
            $table->date('tgl_harus_kembali');
            $table->date('tgl_diajukan_kembali')->nullable();
            $table->enum('status', [
                'diajukan',
                'ditolak',
                'dipinjam',
                'menunggu_verifikasi',
                'selesai',
            ])->default('diajukan')->index();
            $table->text('keperluan')->nullable();
            $table->text('alasan_tolak')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
