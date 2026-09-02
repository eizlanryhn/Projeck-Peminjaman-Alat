<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_after_insert');

        DB::unprepared("
            CREATE TRIGGER trg_pengembalian_before_insert
            BEFORE INSERT ON pengembalian
            FOR EACH ROW
            BEGIN
                DECLARE v_tgl_harus_kembali DATE;
                DECLARE v_tarif DECIMAL(12,2);
                DECLARE v_denda DECIMAL(12,2);
                DECLARE v_hari INT;

                SELECT CAST(nilai AS DECIMAL(12,2)) INTO v_tarif
                FROM pengaturan WHERE kunci = 'tarif_denda_harian';

                IF v_tarif IS NULL THEN
                    SET v_tarif = 0;
                END IF;

                SELECT tgl_harus_kembali INTO v_tgl_harus_kembali
                FROM peminjaman WHERE id = NEW.peminjaman_id;

                SET v_hari = DATEDIFF(NEW.tgl_kembali, v_tgl_harus_kembali);
                IF v_hari < 0 THEN
                    SET v_hari = 0;
                END IF;

                UPDATE detail_peminjaman
                SET denda = fn_hitung_denda(
                    v_tgl_harus_kembali,
                    NEW.tgl_kembali,
                    jumlah,
                    v_tarif
                )
                WHERE peminjaman_id = NEW.peminjaman_id;

                SELECT COALESCE(SUM(denda), 0) INTO v_denda
                FROM detail_peminjaman
                WHERE peminjaman_id = NEW.peminjaman_id;

                SET NEW.hari_terlambat = v_hari;
                SET NEW.denda = v_denda;
                SET NEW.total_denda = v_denda + COALESCE(NEW.denda_kerusakan, 0);
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_pengembalian_after_insert
            AFTER INSERT ON pengembalian
            FOR EACH ROW
            BEGIN
                DECLARE v_kode VARCHAR(30);

                UPDATE alat a
                JOIN detail_peminjaman d ON d.alat_id = a.id
                SET a.stok_tersedia = a.stok_tersedia + d.jumlah
                WHERE d.peminjaman_id = NEW.peminjaman_id
                  AND d.kondisi_kembali IN ('baik', 'rusak_ringan');

                UPDATE alat a
                JOIN detail_peminjaman d ON d.alat_id = a.id
                SET a.stok = a.stok - d.jumlah
                WHERE d.peminjaman_id = NEW.peminjaman_id
                  AND d.kondisi_kembali IN ('rusak_berat', 'hilang');

                UPDATE peminjaman
                SET status = 'selesai',
                    updated_at = NOW()
                WHERE id = NEW.peminjaman_id;

                SELECT kode_pinjam INTO v_kode
                FROM peminjaman WHERE id = NEW.peminjaman_id;

                INSERT INTO log_aktivitas
                    (user_id, aksi, tabel_tujuan, deskripsi, created_at)
                VALUES
                    (NEW.petugas_id, 'verifikasi_kembali', 'pengembalian',
                    CONCAT('Memverifikasi pengembalian ', v_kode,
                    ' dengan total denda ', NEW.total_denda),
                    NOW());
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_before_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_after_insert');
    }
};
