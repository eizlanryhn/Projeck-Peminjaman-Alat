<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')
                ->constrained('peminjaman')
                ->cascadeOnDelete();
            $table->foreignId('alat_id')
                ->constrained('alat')
                ->restrictOnDelete();
            $table->integer('jumlah');
            $table->enum('kondisi_kembali', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
                'hilang',
            ])->nullable();
            $table->decimal('denda', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['peminjaman_id', 'alat_id']);
        });

        DB::statement('ALTER TABLE detail_peminjaman ADD CONSTRAINT chk_detail_jumlah CHECK (jumlah > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};
