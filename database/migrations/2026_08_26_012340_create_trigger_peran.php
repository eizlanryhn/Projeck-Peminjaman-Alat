<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_beri_peran');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_cabut_peran');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_ubah_peran');

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_beri_peran
        AFTER INSERT ON model_has_roles
        FOR EACH ROW
        BEGIN
            INSERT INTO log_aktivitas (user_id, aksi, tabel_tujuan, deskripsi, ip_address, created_at)
            VALUES (
                NEW.model_id,
                'beri_peran',
                'model_has_roles',
                CONCAT('Pengguna id ', NEW.model_id, ' diberi peran id ', NEW.role_id),
                NULL,
                NOW()
            );
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_cabut_peran
        AFTER DELETE ON model_has_roles
        FOR EACH ROW
        BEGIN
            INSERT INTO log_aktivitas (user_id, aksi, tabel_tujuan, deskripsi, ip_address, created_at)
            VALUES (
                OLD.model_id,
                'cabut_peran',
                'model_has_roles',
                CONCAT('Peran id ', OLD.role_id, ' dicabut dari pengguna id ', OLD.model_id),
                NULL,
                NOW()
            );
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_ubah_peran
        AFTER UPDATE ON model_has_roles
        FOR EACH ROW
        BEGIN
            INSERT INTO log_aktivitas (user_id, aksi, tabel_tujuan, deskripsi, ip_address, created_at)
            VALUES (
                NEW.model_id,
                'ubah_peran',
                'model_has_roles',
                CONCAT('Peran pengguna id ', NEW.model_id, ' diubah'),
                NULL,
                NOW()
            );
        END
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_beri_peran');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_cabut_peran');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_ubah_peran');
    }
};
