<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengaturan_before_delete');

        DB::unprepared("
            CREATE TRIGGER trg_pengaturan_before_delete
            BEFORE DELETE ON pengaturan
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Data pengaturan tidak dapat dihapus, hanya dapat diubah';
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengaturan_before_delete');
    }
};
