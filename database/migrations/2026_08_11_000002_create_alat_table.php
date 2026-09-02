<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->restrictOnDelete();
            $table->string('kode_alat', 30)->unique();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->integer('stok')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])
                ->default('baik');
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE alat ADD CONSTRAINT chk_alat_stok CHECK (stok >= 0)');
        DB::statement('ALTER TABLE alat ADD CONSTRAINT chk_alat_tersedia CHECK (stok_tersedia >= 0 AND stok_tersedia <= stok)');
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
