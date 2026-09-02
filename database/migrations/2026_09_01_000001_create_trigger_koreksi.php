<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_peminjaman_before_delete');

        DB::unprepared("
            CREATE TRIGGER trg_peminjaman_before_delete
            BEFORE DELETE ON peminjaman
            FOR EACH ROW
            BEGIN
                IF OLD.status NOT IN ('diajukan', 'ditolak') THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Peminjaman yang sudah diproses tidak dapat dihapus';
                END IF;
            END
        ");

        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_before_delete');

        DB::unprepared("
            CREATE TRIGGER trg_pengembalian_before_delete
            BEFORE DELETE ON pengembalian
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Data pengembalian tidak dapat dihapus';
            END
        ");

        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_before_update');

        DB::unprepared("
            CREATE TRIGGER trg_pengembalian_before_update
            BEFORE UPDATE ON pengembalian
            FOR EACH ROW
            BEGIN
                -- Denda keterlambatan dan hari terlambat adalah hasil
                -- perhitungan saat verifikasi, tidak boleh dikoreksi manual.
                SET NEW.hari_terlambat = OLD.hari_terlambat;
                SET NEW.denda          = OLD.denda;

                -- BR-09: total denda selalu dihitung ulang oleh sistem.
                SET NEW.total_denda = OLD.denda + COALESCE(NEW.denda_kerusakan, 0);
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_before_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_before_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_peminjaman_before_delete');
    }
};
