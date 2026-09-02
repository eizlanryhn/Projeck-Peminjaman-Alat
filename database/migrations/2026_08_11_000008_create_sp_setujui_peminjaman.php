<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_setujui_peminjaman');

        DB::unprepared("
            CREATE PROCEDURE sp_setujui_peminjaman(
                IN p_peminjaman_id BIGINT UNSIGNED,
                IN p_petugas_id BIGINT UNSIGNED
            )
            BEGIN
                DECLARE v_selesai INT DEFAULT 0;
                DECLARE v_alat_id BIGINT UNSIGNED;
                DECLARE v_jumlah INT;
                DECLARE v_tersedia INT;
                DECLARE v_nama_alat VARCHAR(150);
                DECLARE v_status VARCHAR(30);
                DECLARE v_pesan VARCHAR(255);
                DECLARE v_kode VARCHAR(30);

                DECLARE kursor_detail CURSOR FOR
                    SELECT alat_id, jumlah
                    FROM detail_peminjaman
                    WHERE peminjaman_id = p_peminjaman_id;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_selesai = 1;

                -- Pemeriksaan 1: status harus masih diajukan.
                SELECT status, kode_pinjam INTO v_status, v_kode
                FROM peminjaman WHERE id = p_peminjaman_id;

                IF v_status IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Data peminjaman tidak ditemukan';
                END IF;

                IF v_status <> 'diajukan' THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Peminjaman ini sudah pernah diproses';
                END IF;

                -- Pemeriksaan 2: seluruh baris alat harus mencukupi stoknya.
                OPEN kursor_detail;

                periksa_stok: LOOP
                    FETCH kursor_detail INTO v_alat_id, v_jumlah;

                    IF v_selesai = 1 THEN
                        LEAVE periksa_stok;
                    END IF;

                    SELECT stok_tersedia, nama
                    INTO v_tersedia, v_nama_alat
                    FROM alat WHERE id = v_alat_id FOR UPDATE;

                    IF v_tersedia < v_jumlah THEN
                        SET v_pesan = CONCAT(
                            'Stok tidak mencukupi untuk ', v_nama_alat,
                            ' (diminta ', v_jumlah, ', tersedia ', v_tersedia, ')'
                        );

                        CLOSE kursor_detail;

                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = v_pesan;
                    END IF;
                END LOOP;

                CLOSE kursor_detail;

                -- Semua baris lolos: kurangi stok sekaligus.
                UPDATE alat a
                JOIN detail_peminjaman d ON d.alat_id = a.id
                SET a.stok_tersedia = a.stok_tersedia - d.jumlah
                WHERE d.peminjaman_id = p_peminjaman_id;

                UPDATE peminjaman
                SET status = 'dipinjam',
                    petugas_id = p_petugas_id,
                    updated_at = NOW()
                WHERE id = p_peminjaman_id;

                INSERT INTO log_aktivitas
                    (user_id, aksi, tabel_tujuan, deskripsi, created_at)
                VALUES
                    (p_petugas_id, 'setujui', 'peminjaman',
                    CONCAT('Menyetujui peminjaman ', v_kode), NOW());
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_setujui_peminjaman');
    }
};
