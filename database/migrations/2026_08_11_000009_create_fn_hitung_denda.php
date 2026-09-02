<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS fn_hitung_denda');

        DB::unprepared("
            CREATE FUNCTION fn_hitung_denda(
                p_tgl_harus_kembali DATE,
                p_tgl_kembali DATE,
                p_jumlah INT,
                p_tarif_harian DECIMAL(12,2)
            )
            RETURNS DECIMAL(12,2)
            DETERMINISTIC
            BEGIN
                DECLARE v_hari_terlambat INT;

                SET v_hari_terlambat = DATEDIFF(p_tgl_kembali, p_tgl_harus_kembali);

                IF v_hari_terlambat <= 0 THEN
                    RETURN 0;
                END IF;

                RETURN v_hari_terlambat * p_tarif_harian * p_jumlah;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS fn_hitung_denda');
    }
};
