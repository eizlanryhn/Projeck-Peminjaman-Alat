<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')
                ->unique()
                ->constrained('peminjaman')
                ->restrictOnDelete();
            $table->foreignId('petugas_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->date('tgl_kembali');
            $table->integer('hari_terlambat')->default(0);
            $table->decimal('denda', 12, 2)->default(0);
            $table->decimal('denda_kerusakan', 12, 2)->default(0);
            $table->decimal('total_denda', 12, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
