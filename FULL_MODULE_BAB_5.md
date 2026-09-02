
=========================================
PAGE 1
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
1

BAB V
PENGEMBALIAN & DENDA

5.1
 
Function Denda dan Dua Trigger Pengembalian

5.1.1)
 
Buat function penghitung denda

Pada terminal buat perintah migration :

php artisan make:migration create_fn_hitung_denda

Buka file
 
database\migrations\..._create_fn_hitung_denda.php :

Tiga hal yang perlu dipahami:

Bagian
 
Penjelasan

RETURNS DECIMAL(12,2)
 
Function selalu mengembalikan satu nilai. Inilah bedanya dengan
procedure, yang mengerjakan sesuatu tanpa harus mengembalikan
apa pun

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_1_img_1.png] ---
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
public function up(): void
{
DB ::unprepared(*DROP FUNCTION IF EXISTS fn_hitung_denda');
DB ::unprepared(*
CREATE FUNCTION fn_hitung_denda(
p_tgl_harus_kembali DATE,
p_tgl_kembali DATE,
p_jumtan NT,
p_tarif_harian DECIMAL(12,2)
)
RETURNS DECIMAL(12,2)
DETERMINISTIC
BEGIN
DECLARE v_hari_terlambat INT;
- BR-85: dihitung per hari kalender,
— aknir pekan dan hari libur ikut terhitung.
SET v_hari_terlambat = DATEDIFF(p_tgl kembali, p_tgl_harus_Kembali);
IF v_hari_terlambat < 8 THEN
RETURN 0;
END IF;
— BR-84: hari_terlambat x tarif_harian x jumlah.
RETURN v_hari_terlambat * p_tarif_harian * p_jumlan;
END
=z
+
public function down(): void
{
DB ::unprepared(*DROP FUNCTION IF EXISTS fn_hitung_denda');
+
fi]


=========================================
PAGE 2
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
2

Bagian
 
Penjelasan

DETERMINISTIC
 
Janji bahwa masukan yang sama selalu menghasilkan keluaran yang
sama. MariaDB menolak membuat function tanpa keterangan ini
bila log biner aktif

IF v_hari_terlambat <= 0
THEN RETURN 0

Pengembalian tepat waktu atau lebih awal tidak menghasilkan
denda negatif. Tanpa penjagaan ini, mengembalikan alat 3 hari lebih
cepat akan menghasilkan denda minus, dan sistem justru berutang
kepada peminjam

Tarif dikirim sebagai parameter, bukan dibaca langsung di dalam function. Dengan begitu function
ini murni menghitung, tidak bergantung pada isi tabel mana pun, sehingga jauh lebih mudah diuji.

Jalankan:

php artisan migrate

5.1.2)
 
Uji function di phpMyAdmin

Buka tab SQL, jalankan satu per satu:

--
 
Terlambat 4 hari, 2 unit, tarif 5000 → 40000

SELECT fn_hitung_denda('2026-08-01', '2026-08-05', 2, 5000) AS hasil;

--
 
Tepat waktu → 0

SELECT fn_hitung_denda('2026-08-05', '2026-08-05', 3, 5000) AS hasil;

--
 
Dikembalikan lebih awal → 0, bukan bilangan negatif

SELECT fn_hitung_denda('2026-08-10', '2026-08-05', 1, 5000) AS hasil;

--
 
Terlambat 1 hari, 1 unit → 5000

SELECT fn_hitung_denda('2026-08-01', '2026-08-02', 1, 5000) AS hasil;

Keempat hasilnya harus 40000, 0, 0, dan 5000. Bila ada yang meleset, perbaiki sekarang
 
—
 
jangan
lanjut ke trigger, karena trigger akan memakai function ini.

5.1.3)
 
Buat trigger BEFORE INSERT

Pada terminal buat migration :

php artisan make:migration create_trigger_pengembalian

Buka file
 
database\migrations\2026_08_22_143900_create_trigger_pengembalian.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_2_img_2.png] ---
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
h public function up(): void
N DB ::unprepared(*DROP TRIGGER IF EXISTS trg_pengembalian_before_insert');


=========================================
PAGE 3
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
3

Kenapa BEFORE, bukan AFTER?
 
Karena trigger ini mengisi kolom pada baris yang sedang
disisipkan. Setelah baris tersimpan,
 
NEW
 
tidak dapat diubah lagi. Segala sesuatu yang ingin ditulis ke
baris itu sendiri harus dikerjakan di BEFORE.

Kenapa denda tiap baris ditulis ke
 
detail_peminjaman
, bukan hanya dijumlahkan? Karena
peminjam berhak tahu denda datang dari alat yang mana. Rincian per alat itulah yang akan
ditampilkan dicetak pada laporan.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_3_img_3.png] ---
a
12 DB ::unprepared(*

13 CREATE TRIGGER trg_pengembalian_before_insert

14 BEFORE INSERT ON pengembalian

15 FOR EACH ROW

16 BEGIN

17 DECLARE v_tgl_harus_kembali DATE;

18 DECLARE v_tarif DECIMAL(12,2);

19 DECLARE v_denda DECIMAL(12,2);

20 DECLARE v_hari INT;

21

22 -— NFR-88: tarif dibaca langsung dari tabel pengaturan,
23 — bukan dari nilai tetap di dalam kode.

24 SELECT CAST(nilai AS DECIMAL(12,2)) INTO v_tarif

25 FROM pengaturan WHERE kunci = ‘tarif_denda_harian’;

26

27 IF v_tarif IS NULL THEN

28 SET v_tarif = 6;

29 END IF;

30

31 SELECT tgl_harus_kembali INTO v_tgl_harus_kembali

32 FROM peminjaman WHERE id = NEW.peminjaman_id;

33

34 SET v_hari = DATEDIFF(NEW.tgl_kembali, v_tgl_harus_kembali);
35 IF v_hari < 8 THEN

36 SET v_hari = 8;

37 END IF;


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_3_img_4.png] ---
a
39 —- Isi kolom denda pada tiap baris alat.
40 UPDATE detail_peminjaman
a SET denda = fn_nitung_denda(
42 v_tgl_harus_kembali,
43 NEW. tgl_kembali,
4 jumlan,
45 v_tarif
46 )
47 WHERE peminjaman_id = NEW.peminjaman_id;
48
49 — Jumlahkan denda seluruh baris menjadi denda transaksi.
50 SELECT COALESCE(SUM(denda), 8) INTO v_denda
51 FROM detail_peminjaman
52 WHERE peminjaman_id = NEW.peminjaman_id;
53
54 SET NEW.hari_terlambat = v_hari;
55 SET NEW.denda = v_denda;
56
57 -- BR-89: total _denda dihitung sistem, bukan diketik petugas.
58 SET NEW.total_denda = v_denda + COALESCE(NEW.denda_kerusakan, 0);
59 END
6 2%:
61 +
62
63 public function down(): void
64 1
65 DB ::unprepared(*DROP TRIGGER IF EXISTS trg_pengembalian_before_insert');
66 +
EEE. H
a


=========================================
PAGE 4
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
4

Perhatikan
 
COALESCE(NEW.denda_kerusakan, 0)
. Petugas mungkin mengosongkan isian denda
kerusakan. Tanpa
 
COALESCE
,
 
v_denda + NULL
 
menghasilkan NULL, dan total denda menjadi kosong,
bukan sama dengan denda keterlambatan.

5.1.4)
 
Buat trigger AFTER INSERT

Tambahkan pada berkas migration yang sama, di bawah trigger sebelumnya di dalam method
 
up()
:

DB::unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_after_insert');

DB::unprepared("

CREATE TRIGGER trg_pengembalian_after_insert

AFTER INSERT ON pengembalian

FOR EACH ROW

BEGIN

DECLARE v_kode VARCHAR(20);

-- BR-02: alat berkondisi baik dan rusak ringan

-- kembali menambah stok tersedia.

UPDATE alat a

JOIN detail_peminjaman d ON d.alat_id = a.id

SET a.stok_tersedia = a.stok_tersedia + d.jumlah

WHERE d.peminjaman_id = NEW.peminjaman_id

AND d.kondisi_kembali IN ('baik', 'rusak_ringan');

-- BR-07: alat rusak berat dan hilang tidak kembali

-- ke stok tersedia, dan stok total berkurang.

UPDATE alat a

JOIN detail_peminjaman d ON d.alat_id = a.id

SET a.stok = a.stok - d.jumlah

WHERE d.peminjaman_id = NEW.peminjaman_id

AND d.kondisi_kembali IN ('rusak_berat', 'hilang');

UPDATE peminjaman

SET status
 
= 'selesai',

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

=========================================
PAGE 5
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
5

Lengkapi juga method
 
down()
 
:

Kenapa AFTER, bukan BEFORE?
 
Trigger ini mengubah tabel lain
 
—
 
alat
 
dan
 
peminjaman
 
—
 
dan
hanya boleh berjalan bila baris pengembalian benar-benar berhasil tersimpan. Kalau ditaruh di
BEFORE lalu penyimpanan gagal karena suatu hal, stok terlanjur bertambah padahal
pengembaliannya tidak pernah tercatat.

Perhatikan dua perintah UPDATE yang terpisah
. Baris berkondisi
 
rusak_ringan
 
masuk
kelompok pertama: alatnya tetap kembali ke stok, karena masih bisa dipakai. Hanya
 
rusak_berat

dan
 
hilang
 
yang membuat stok total berkurang.

Kenapa status diubah di trigger, bukan di PHP?
 
Tabel pembagian tanggung jawab yang Anda
susun menetapkan bahwa perubahan status dimiliki basis data. Satu peristiwa, satu pemilik. Kalau
PHP juga mengubah status, akan sulit melacak dari mana perubahan sebenarnya datang.

Jalankan migration:

php artisan migrate

Periksa di phpMyAdmin, tab
 
Triggers
:
 
trg_pengembalian_before_insert
 
dan

trg_pengembalian_after_insert
 
harus terdaftar. Tab Routines harus memuat
 
sp_setujui_peminjaman

dan
 
fn_hitung_denda
.

5.1.5)
 
Uji kedua trigger lewat SQL murni

Ini bagian paling penting dari job sheet ini. Kerjakan berurutan dan catat setiap hasilnya.

Persiapan
. Pastikan ada satu peminjaman berstatus
 
dipinjam
. Bila belum ada, buat lewat
antarmuka: ajukan sebagai peminjam, setujui sebagai petugas. Lalu catat datanya:

SELECT id, kode_pinjam, status, tgl_harus_kembali

FROM peminjaman WHERE status = 'dipinjam';

SELECT d.id, d.alat_id, d.jumlah, d.kondisi_kembali, d.denda,

a.nama, a.stok, a.stok_tersedia

FROM detail_peminjaman d

JOIN alat a ON a.id = d.alat_id

WHERE d.peminjaman_id = 1;

Ganti angka 1 dengan id peminjaman Anda.
 
Ambil tangkapan layar hasil ini
 
sebagai keadaan
sebelum.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_5_img_5.png] ---
public function down(): void
{
DB: :unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_before_insert"');
DB: :unprepared('DROP TRIGGER IF EXISTS trg_pengembalian_after_insert");
}


=========================================
PAGE 6
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
6

Uji 1
 
—
 
Pengembalian tepat waktu, seluruh alat kondisi baik.

-- Langkah 1: isi kondisi kembali lebih dulu.

UPDATE detail_peminjaman

SET kondisi_kembali = 'baik'

WHERE peminjaman_id = 1;

-- Langkah 2: sisipkan baris pengembalian.

INSERT INTO pengembalian

(peminjaman_id, petugas_id, tgl_kembali, denda_kerusakan, catatan, created_at,
updated_at)

VALUES

(1, 2, (SELECT tgl_harus_kembali FROM peminjaman WHERE id = 1), 0, 'Uji tepat waktu',
NOW(), NOW());

Periksa hasilnya:

SELECT * FROM pengembalian WHERE peminjaman_id = 1;

SELECT status FROM peminjaman WHERE id = 1;

SELECT id, nama, stok, stok_tersedia FROM alat;

Yang harus terjadi:
 
hari_terlambat
 
bernilai 0,
 
denda
 
bernilai 0, total_denda bernilai 0, status
peminjaman menjadi
 
selesai
, dan
 
stok_tersedia
 
kembali seperti sebelum dipinjam.

Uji 2
 
—
 
Pengembalian terlambat dengan satu alat rusak berat.

Siapkan peminjaman kedua yang berstatus dipinjam dan berisi minimal dua jenis alat. Lalu:

-- Buat seolah-olah sudah lewat tenggat 3 hari.

UPDATE peminjaman

SET tgl_harus_kembali = DATE_SUB(CURDATE(), INTERVAL 3 DAY)

WHERE id = 2;

-- Langkah 1: kondisi berbeda tiap baris.

UPDATE detail_peminjaman SET kondisi_kembali = 'baik'
 
WHERE id = 3;

UPDATE detail_peminjaman SET kondisi_kembali = 'rusak_berat' WHERE id = 4;

-- Langkah 2: sisipkan pengembalian dengan denda kerusakan.

INSERT INTO pengembalian

(peminjaman_id, petugas_id, tgl_kembali, denda_kerusakan, catatan, created_at,
updated_at)

VALUES

(2, 2, CURDATE(), 150000, 'Uji terlambat dan rusak', NOW(), NOW());

Ganti angka id detail sesuai data Anda. Lalu periksa:

SELECT hari_terlambat, denda, denda_kerusakan, total_denda

FROM pengembalian WHERE peminjaman_id = 2;

=========================================
PAGE 7
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
7

SELECT d.id, d.jumlah, d.kondisi_kembali, d.denda, a.nama, a.stok, a.stok_tersedia

FROM detail_peminjaman d JOIN alat a ON a.id = d.alat_id

WHERE d.peminjaman_id = 2;

Yang harus terjadi:

Yang diperiksa
 
Nilai yang benar

hari_terlambat
 
3

denda tiap baris detail
 
3 x 5000 x jumlah baris itu

denda pada pengembalian
 
jumlah denda seluruh baris

total_denda
 
denda + 150000

Alat berkondisi baik
 
stok_tersedia bertambah, stok tetap

Alat berkondisi rusak_berat
 
stok_tersedia tidak bertambah, stok berkurang

Status peminjaman
 
selesai

log_aktivitas
 
Bertambah satu baris beraksi verifikasi_kembali

Uji 3
 
—
 
Buktikan urutan langkah tidak boleh dibalik.

Siapkan satu peminjaman
 
dipinjam
 
lagi, lalu kerjakan terbalik: sisipkan baris pengembalian dulu,
baru isi kondisi kembali.

INSERT INTO pengembalian

(peminjaman_id, petugas_id, tgl_kembali, denda_kerusakan, catatan, created_at,
updated_at)

VALUES

(3, 2, CURDATE(), 0, 'Uji urutan terbalik', NOW(), NOW());

UPDATE detail_peminjaman SET kondisi_kembali = 'baik' WHERE peminjaman_id = 3;

Periksa
 
stok_tersedia
 
alat terkait. Stoknya tidak bertambah sama sekali, dan tidak ada satu pun
pesan kesalahan yang muncul.

Penyebabnya: saat trigger AFTER INSERT berjalan, kolom
 
kondisi_kembali
 
masih kosong, sehingga
tidak ada baris yang cocok dengan syarat
 
IN ('baik', 'rusak_ringan')
. Trigger tetap berjalan,
hanya tidak menemukan apa pun untuk diperbarui.

Inilah jenis kesalahan yang paling sulit dilacak: tidak ada pesan error, aplikasi tampak berjalan
normal, tetapi angka stok perlahan menjadi salah. Ambil tangkapan layarnya dan simpan
 
—
 
bukti
ini yang membuat urutan wajib masuk akal, bukan sekadar hafalan.

Bersihkan data uji.

php artisan migrate:fresh --seed

php artisan permission:cache-reset

=========================================
PAGE 8
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
8

5.2
 
Layanan Pengembalian dan Verifikasi Petugas

5.2.1)
 
Lengkapi model Pengembalian

Buka
 
app/Models/Pengembalian.php
, tambahkan relasi:

Ingat kembali bahwa
 
hari_terlambat
,
 
denda
, dan
 
total_denda
 
sengaja tidak ada di
 
$fillable
.
Ketiganya milik trigger. Bila suatu saat Anda mendapati nilainya selalu nol, jangan
menambahkannya ke
 
$fillable
 
—
 
periksa dulu apakah triggernya benar-benar terpasang.

5.2.2)
 
Tambahkan method pengajuan pengembalian ke layanan

Buka
 
app/Services/PeminjamanService.php
, tambahkan:

Kolom
 
tgl_diajukan_kembali
 
diisi tanggal hari ini. Inilah penerapan dasar perhitungan denda
adalah tanggal peminjam menekan tombol, bukan tanggal petugas sempat memverifikasi.
Peminjam yang mengembalikan tepat waktu tidak boleh terkena denda hanya karena petugasnya
baru sempat memeriksa dua hari kemudian.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_8_img_6.png] ---
public function peminjaman()
{
return $this—belongsTo(Peminjaman:: class, 'peminjaman_id');
+
public function petugas()
{
return $this—belongsTo(User::class, 'petugas_id');
}


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_8_img_7.png] ---
public function ajukanPengembalian(Peminjaman $peminjaman): void
{
abort_untess(
$peminjaman—sstatus—bolenKe(StatusPeminjanan :: HenungguVerifikasi)
422,
‘Peminjaman ini tidak dalam keadaan dipinjam.'
):
DB ::transaction(function () use ($peminjaman) {
$peminjaman—update ([
‘status’ = StatusPeminjaman ::MenungguVerifikasi,
*tgl_diajukan_kembali' => now()—>toDatestring(),
n;
LogAktivitas::create([
user_id* = auth()—id0),
‘aksit = ‘ajukan_kembali’,
*tabel_tujuan* = ‘peminjaman’,
‘deskripsi' = 'Mengajukan pengembalian ' . $peminjaman—skode_pinjam,
'ip_address’ = request()—ip(),
n;
BH;
}


=========================================
PAGE 9
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
9

5.2.3)
 
Buat kelas layanan pengembalian

Buat berkas
 
app/Services/PengembalianService.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_9_img_8.png] ---
<?php
namespace App\Services;
use App\Enums\StatusPeminjaman;
use App\Models\DetailPeminjaman;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Support\Facades\DB;
class Pengembalianservice
vi
public function antrianVerifikasi()
vq
“ return Peminjaman ::with([*peminjam', 'detail.alat'])
—where('status', StatusPeminjaman::MenungguVerifikasi—value)
—orderBy (* tgl_diajukan_kembali')
—paginate(18);
+
public function daftarSedangdipinjan()
~ {
“ return Peminjaman ::with([*peminjam', 'detail.alat'])
~ —whereIn('status’, [
statusPeminjaman:: Dipinjan—yvalue,
StatusPeminjaman:: Menungguverifikasi—value,
n
—orderBy (* tgl_harus_kembali')
—paginate(18);
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_9_img_9.png] ---
public function verifikasi(

Peninjaman $peminjaman,

int $petugasId,

array $kondisiPerBaris,

string $tgliembali,

float $dendaKerusakan,

?string $catatan = null

): Pengembalian {

abort_untess(
$peminjaman—status—bolehKe(StatusPeminjaman :: Selesai),
422,
‘Peminjaman ini belum diajukan untuk dikembalikan.'

):

DB ::beginTransaction();

try {
foreach ($kondisiPerBaris as $detailld = $kondisi) {

DetailPeminjaman::where('id", $detailld)
—where(*peminjaman_id*, $peminjaman—id)
—update(['kondisi_kembali' = $kondisil);

3


=========================================
PAGE 10
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
10

DB::beginTransaction()
 
yang membungkus keduanya. Bila penyisipan baris pengembalian gagal,
pengisian kondisi kembali ikut dibatalkan. Tanpa transaksi, akan tersisa peminjaman berstatus

dipinjam
 
yang baris detailnya sudah terisi kondisi kembali
 
—
 
data setengah jadi yang
membingungkan.

$pengembalian->fresh()
. Objek hasil
 
create()
 
masih berisi nilai yang dikirim PHP, sementara

hari_terlambat
,
 
denda
, dan
 
total_denda
 
baru diisi oleh trigger di dalam basis data. Tanpa membaca
ulang, halaman rincian akan menampilkan angka nol untuk ketiganya. Kelupaan ini adalah salah
satu kesalahan paling sering pada modul pengembalian.

5.2.4)
 
Buat controller

Pada terminal buat controller :

php artisan make:controller PengembalianController

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_10_img_10.png] ---
$tglbiajukan = $peminjaman—tgl_diajukan_kembali?—toDatestring();
if ($tglDiajukan && $tglDiajukan == $tglkembali) {
LogAktivitas:: create ([
‘user_id" = $petugasId,
‘aksi’ = ‘koreksi_tgl_kembali’,
*tabel_tujuan' = ‘pengembalian’,
‘deskripsi' = 'Tanggal kembali ' . $peminjaman—skode_pinjam
. * dikoreksi dari ' . $tglDiajukan
. ' menjadi ' . $tglKembali,
*ip_address’ = request()—ip(),
103
3
$pengembalian = Pengembalian::create([
‘peminjaman_id' = $peminjaman—>id,
*petugas_ia" = $petugasd,
"tgl_kembali* = $tglKembali,
"denda_kerusakan' = $dendaKerusakan,
"catatan’ = $catatan,
n;
DB: commit();
} catch (\Throwable $e) {
DB::rollBack();
throw $e;
+
return $pengembalian—fresh();
H
+


=========================================
PAGE 11
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
11

Buka file
 
app\Http\Controllers\PengembalianController.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_11.png] ---
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\services\Peminjamanservice;
use App\Services\PengembalianService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class PengembalianController extends Controller
{
public function _construct(
private PengembalianService $layanan,
private PeminjamanService $layananPeminjaman
) {+
public function ajukan(Peminjaman $peminjaman)
{
abort_unless($peminjaman—user_id == auth()—id(), 483);
$this—layananPeminjaman—ajukanPengembalian($peminjaman);
return redirect ()
—route(*peninjanan.saya*)
—with('sukses', 'Pengembalian ' . $peminjaman—kode_pinjam
. * diajukan. Menunggu verifikasi petugas.');
+
public function pantau()
{
$daftarPeminjaman = $this—layanan—daftarSedangDipinjam();
return view('pengembalian.pantau', compact('daftarPeminjaman'));
+
public function antrian()
{
$daftarAntrian = $this—layanan—antrianverifikasi();
return view('pengembalian.antrian’, compact('daftarAntrian’));
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_12.png] ---
public function formverifikasi(Peminjaman $peminjaman)
{

$peminjaman—1oad(['peminjam’, 'detail.alat']);

return view(*pengembalian.form*, compact (’peminjaman’));
+


=========================================
PAGE 12
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
12

Aturan
 
'size:' . count($daftarDetailId)
 
memastikan petugas mengisi kondisi untuk
 
setiap
 
alat,
tidak boleh ada yang dilewati. Kalau ada satu saja yang kosong, alat itu tidak akan kembali ke stok

—
 
dan tidak ada yang memberi tahu.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_12_img_13.png] ---
public function simpanVerifikasi(Request $request, Peminjaman $peminjaman)
{
$daftarDetailld = $peminjaman—detail—pluck('id')—all();
$data = $request—validate([
*tgl_kembali = ['required’, 'date'l,
‘denda_kerusakan' => [*nullable’, ‘numeric’, ‘min:e'l,
‘catatan’ = ['nullable’, 'string’, 'max:500'],
*kondisi® = ['required’, ‘array’, ‘size:' . count($daftarDetailld)],
'Kondisi.** =
“required,
Rule:zin(['baik’, 'rusak_ringan', ‘rusak_berat', ‘hilang'l),
jis
1,
*kondisi.required' => ‘Kondisi setiap alat wajib diisi.’,
*kondisi.size’ = 'Kondisi setiap alat wajib diisi.®,
n;
$pengembalian = $this—layanan—verifikasi(
$peminjaman,
auth()—id(),
$data *kondisi'],
$datal 'tgl_kembali'l,
(float) ($datal'denda_kerusakan'] 2? 8),
$data[*catatan'] 2? null
):
return redirect()
—route(’pengembalian.rincian’, $pengembalian)
—with('sukses', 'Pengembalian berhasil diverifikasi.');
+
public function rincian(Pengembalian $pengembalian)
{
$pengembalian—1load(['peminjaman.detail.alat’, 'peminjaman.peminjam', ‘petugas']);
return view('pengembalian.rincian’, compact('pengembalian‘));
+
}


=========================================
PAGE 13
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
13

5.2.5)
 
Daftarkan route dan tombol

Edit file
 
routes\web.php
 
:

Route
 
rincian
 
diberi awalan
 
/rincian/
 
agar tidak bertabrakan dengan
 
{peminjaman}.
 
Tanpa
pembeda itu, Laravel tidak dapat menentukan alamat mana yang dimaksud.

Tambahkan menu petugas di Navbar:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_13_img_14.png] ---
(loco
use App\Http\Controllers\PengembalianController;
(loco
Route: :middleware([ "auth'])->group (function () {
Moooc
Route: :middleware( 'permission:peminjaman.kembalikan")
->post(
*/peminjaman/{peminjaman}/kembalikan’,
[PengembalianController::class, ‘ajukan']
)
->name( 'pengembalian.ajukan’);
Route: :middleware( 'permission:pengembalian.pantau’)
->prefix('pengembalian")
->name( 'pengembalian.")
->group (function () {
Route: :get('/pantau’, [PengembalianController::class, ‘pantau’])->name('pantau’);
Route: :get('/antrian', [PengembalianController::class, ‘antrian'])->name('antrian’);
Route: :get(
'/{peminjaman}/verifikasi’,
[PengembalianController::class, ‘formVerifikasi']
)->name( 'verifikasi');
Route: :post(
'/{peminjaman}/verifikasi’,
[PengembalianController::class, ‘simpanVerifikasi®]
)->name( 'simpan’);
Route: :get(
'/rincian/{pengembalian}’,
[PengembalianController::class, ‘rincian’]
)->name('rincian’);
1s
bs


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_13_img_15.png] ---
@can(*pengembalian.pantau’)
<li class="nav-iten">
<a class="nav-link" href="{{ route('pengembalian.pantau') }}">Pemantavan</a>
</li>
<li class="nav-item">
<a class="nav-link" href="{{ route('pengembalian.antrian') }">Verifikasi</a>
</li>
@endcan


=========================================
PAGE 14
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
14

Lalu buka
 
resources\views\peminjaman\tabel-pinjam.blade.php
, tambahkan tombol pada kolom
Aksi:

Tombol hanya muncul saat status dipinjam.

5.2.6)
 
Buat halaman pemantauan dan antrian

Buat
 
resources/views/pengembalian/pantau.blade.php
:

Buat
 
resources\views\pengembalian\tabel-pantau.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_14_img_16.png] ---
<td>
<a href="{{ route("peminjaman.rincian', $peminjaman) }}"
class="btn btn-sm btn-outline-primary*>Rincian</a>
@if ($peminjaman—status == \App\Enums\StatusPeminjaman:: Dipinjam)
<form
method="POST" action="{{ route('pengembalian.ajukan', $peminjaman) }}"
class="d-inline"
onsubmit="return confirm(*Ajukan pengembalian seluruh alat?')">
[Ei
<button type="submit" class="btn btn-sm btn-success">Kembalikan</button>
</form>
endif
</td>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_14_img_17.png] ---
(@extends ('layouts.utama')
@section(*judul', ‘Pemantauan Peminjaman')
@section('konten')
<n4 class="mb-3">Pemantauan Peminjaman Berjalan</hé>
+ <div class="card">
v <div class="card-body">
“ <div class="table-responsive">
@include(*pengembalian.tabel-pantau*)
</div>
{{ $daftarPeminjaman—links() }}
</div>
</div>
@endsection


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_14_img_18.png] ---
<table class="table table-striped align-middle">
<thead>
<tr>
<th>Kode Pinjam</th>
<th>Peminjam</th>
<th>Harus Kembali</th>
<th class="text-center">Jumlah Alat</th>
<th>Status</th>
</tr>
</thead>


=========================================
PAGE 15
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
15

Halaman ini adalah menampilkan alat yang sedang dipinjam beserta yang sudah lewat tenggat.
Baris terlambat diberi latar kuning agar langsung terlihat.

Buat
 
resources/views/pengembalian/antrian.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_15_img_19.png] ---
<tbody>
@forelse ($daftarPeminjaman as $peminjaman)
<tr class="{{ $peminjaman—1lewatTenggat() ? 'table-warning' : '' }}">
<td>{{ $peminjaman—kode_pinjam }}</td>
<td>{{ $peminjaman—peninjan—nama }}</td>
<td>
{{ $peminjaman—tgl_harus_kembali—>format('d/m/Y') }
@if ($peminjaman—lewatTenggat())
<span class="badge bg-danger">
Terlanbat
{{ $peminjaman—tgl_harus_kembali—diffInDays(now()) }} hari
</span>
endif
</td>
<td class="text-center">{{ $peminjaman—detail—count() }}</td>
<td>
<span class="badge bg-{{ $peminjaman—status—warna() }H">
{{ $peminjaman—status—label() }}
</span>
</td>
</tr>
empty
<tr>
<td colspan="5" class="text-center text-muted">
Tidak ada peminjaman yang sedang berjalan.
</td>
</tr>
Qendforelse
+ </tbody>
</table>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_15_img_20.png] ---
(@extends ('layouts.utama')
@section(*judul', ‘Antrian Verifikasi Pengembalian')
@section('konten')
<h4 class="mb-3">Antrian Verifikasi Pengembalian</h4>
<div class="card">
<div class="card-body">
<div class="table-responsive">
@include('pengembalian. tabel-antrian')
</div>
{{ $daftarantrian—links() }
</div>
</div>
@endsection
BE


=========================================
PAGE 16
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
16

Buat
 
resources\views\pengembalian\tabel-antrian.blade.php
 
:

dan sumber datanya
 
$daftarAntrian
, dengan kolom tanggal diajukan kembali:

<td>{{ $peminjaman->tgl_diajukan_kembali?->format('d/m/Y') ?? '-' }}</td>

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_16_img_21.png] ---
<table class="table table-striped align-middle">
<thead>
<tr>
<th>Kode Pinjam</tn>
<th>Peminjam</th>
<th>Diajukan Kembali</th>
<th>Harus Kembali</th>
<th class="text-center”>Jumlah Alat</th>
<th class="text-center">Aksi</th>
</tr>
</thead>
<tbody>
@forelse ($daftarAntrian as $peminjaman)
<tr class="{{ $peminjaman—lewatTenggat() ? 'table-warning' : '' }}">
<td>{{ $peminjaman—kode_pinjam }}</td>
<td>{{ $peminjaman—peminjan—nama }}</td>
<td>
{{ $peminjaman—tgl_diajukan_kembali?—>format(*d/m/y') 22 '-* }}
</td>
<td>
{{ $peminjaman—tgl_harus_kembali—format(*d/m/v') }}
@if ($peminjaman—1lewatTenggat())
<span class="badge bg-danger”>
Terlambat
{{ $peminjaman—tgl_harus_kembali—>diffInDays(now()) }} hari
</span>
@endif
</td>
<td class="text-center">{{ $peminjaman—detail—count() }+</td>
<td class="text-center">
<a href="{{ route('pengembalian.verifikasi', $peminjaman) }}"
class="btn btn-sm btn-primary">Verifikasi</a>
</td>
</tr>
Gempty
<tr>
<td colspan="6" class="text-center text-muted">
Tidak ada pengajuan pengembalian yang menunggu verifikasi.
</td>
</tr>
@endforelse
</tbody>
</table>


=========================================
PAGE 17
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
17

5.2.7)
 
Buat form verifikasi

Buat
 
resources/views/pengembalian/form.blade.php
:

Buat
 
resources\views\pengembalian\kondisi-alat.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_17_img_22.png] ---
@extends('layouts.utama*)
@section(*judul', ‘Verifikasi Pengembalian')
~ @section('konten')
v <div class="d-flex justify-content-between align-items-center mb-3">
<h4 class="mb-8">Verifikasi {{ $peminjaman—kode_pinjam }}</h4>
<a href="{{ route(’pengembalian.antrian') }}" class="btn btn-outline-secondary">Kembali</a>
</div>
~ <form method="P0ST" action="{{ route('pengembalian.simpan', $peminjaman) }}">
eset
“ <div class="row">
“ <div class="col-md-8 mb-3">
@include(*pengembalian.kondisi-alat')
“ <div class="alert alert-info mt-3 mb-8 small">
Alat berkondisi <strong-baik</strong> dan <strong>rusak ringan</strong>
Kembali menambah stok tersedia. Alat berkondisi
<strong>rusak berat</strong> dan <strong>hilang</strong> mengurangi stok total.
</div>
</div>
“ <div class="col-md-4">
@include(*pengembalian.data-pengembalian')
</div>
</div>
</form>
@endsection


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_17_img_23.png] ---
~ <div class="card">
<div class="card-header">Kondisi Alat yang Dikembalikan</div>
v <div class="card-body p-8">
“ <table class="table mb-0 align-middle">
“ <thead>
“ <tr>
<th>Kode</th>
<th>Nama Alat</th>
<th class="text-center">Jumlah</th>
<th style="width: 280px">Kondisi Kembali</th>
</tr>
</thead>
“ <tbody>
~ @foreach ($peminjaman—detail as $baris)
“ <tr>
<td>{{ $baris—alat—kode_alat }}</td>
<td>{{ $baris—alat—nama }}</td>
<td class="text-center">{{ $baris—jumlah } </td>
“ <td>
~ <select name="kondisi[{{ $baris—id }}]" class="form-select form-select-sm" required>
<option value="baik">Baik</option>
<option value="rusak_ringan“>Rusak Ringan</option>
<option value="rusak_berat">Rusak Berat</option>
<option value="hilang">Hilang</option>
</select>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>


=========================================
PAGE 18
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
18

Buat
 
resources\views\pengembalian\data-pengembalian.blade.php
 
:

Perhatikan
 
name="kondisi[{{ $baris->id }}]"
. Tanda kurung siku membuat seluruh isian terkirim
sebagai satu larik dengan id detail sebagai kuncinya. Bentuk inilah yang diterima method

verifikasi()
 
sebagai parameter
 
$kondisiPerBaris
 
dan langsung dipakai di dalam perulangan.

Nilai bawaan Tanggal Kembali diambil dari
 
tgl_diajukan_kembali
, bukan dari tanggal hari ini.

5.2.8)
 
Buat halaman rincian denda

Buat
 
resources/views/pengembalian/rincian.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_18_img_24.png] ---
<div class="card">
<div class="card-header">Data Pengembalian</div>
<div class="card-body">
<dl class="row small mb-3">
<dt class="col-6">Peminjan</dt>
<dd class="col-6">{{ $peminjaman—peminjam—nama }}</dd>
<dt class="col-6">Harus Kembali</dt>
<dd class="col-6">{{ $peminjaman—tgl_harus_kembali—format('d/m/Y') }}</dd>
<dt class="col-6">Diajukan Kembali</dt>
<dd class="col-6">
{{ $peminjaman—tgl_diajukan_kembali?—>format(*d/m/y') 22 '-* }}
</dd>
</d>
<div class="form-text">
Perubahan tanggal akan tercatat di log aktivitas.
</div>
<x-input name="tgl_kembali" type="date" label="Tanggal Kembali"
:value="old('tgl_kembali', $peminjaman—tgl_diajukan_kembali?—>toDateString() ?? now()—toDateString())"
required />
<div class="form-text">
Denda Keterlambatan dihitung sistem, tidak perlu diisi di sini.
</div>
<x-input name="denda_kerusakan" type="number* label="Denda Kerusakan" :value="old('denda_kerusakan', 8)" min="g"
step="1000" />
<div class="mb-3">
<label for="catatan" class="forn-label">Catatan</label>
<textarea class="form-control” id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
</div>
<button type="submit" class="btn btn-success w-160">
simpan Verifikasi
</button>
</div>
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_18_img_25.png] ---
(@extends (*layouts.utama*)
@section(*judul', *Rincian Pengembalian')
@section('konten')
+ <div class="d-flex justify-content-between align-items-center mp-3">
<h4 class="mb-8">Rincian {{ $pengembalian—peminjaman—kode_pinjam }}</n4>
<a href="{{ route('pengembalian.antrian') }}" class="btn btn-outline-secondary">Kembali</a>
</div>
v <div class="row">
v <div class="col-md-8 mb-3">
@include(*pengembalian.tabel-denda')
</div>
v <div class="col-md-4">
@include(*pengembalian.ringkasan-denda')
</div>
</div>
@endsection


=========================================
PAGE 19
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
19

Buat
 
resources\views\pengembalian\tabel-denda.blade.php
 
:

Buat
 
resources\views\pengembalian\ringkasan-denda.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_19_img_26.png] ---
<div class="card">
<div class="card-header">Rincian Denda per Alat</div>
<div class="card-body p-8">
<table class="table mb-9">
<thead>
<tr>
<th>Nama Alat</th>
<th class="text-center">Jumlah</th>
<th>Kondisi</th>
<th class="text-end">Denda</th>
</tr>
</thead>
<tbody>
@foreach ($pengembalian—peminjaman—detail as $baris)
<tr>
<td>{{ $baris—alat—nama }}</td>
<td class="text-center">{{ $baris—jumlah }}</td>
<td>{{ str_replace('_", ' ', $baris—kondisi_kembali) }}</td>
<td class="text-end">
Rp {{ number_format($baris—denda, ©, ',', '.') }
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_19_img_27.png] ---
<div class="card">
<div class="card-header">Ringkasan</div>
<div class="card-body">
<dl class="row mb-8">
<dt class="col-7">Peminjam</dt>
<dd class="col-5 text-end">{{ $pengembalian—peminjaman—peminjam—nama }}</dd>
<dt class="col-7">Tanggal Kembali</dt>
<dd class="col-5 text-end">{{ $pengembalian—tgl_kembali—>format(*d/m/y*) }}</dd>
<dt class="col-7">Hari Terlambat</dt>
<dd class="col-5 text-end">{{ $pengembalian—hari_terlambat }} hari</dd>
<dt class="col-7">Denda Keterlambatan</dt>
<dd class="col-5 text-end">
Rp {{ number_format($pengembalian—denda, 8, *,*, '.') }
</dd>
<dt class="col-7">Denda Kerusakan</dt>
<dd class="col-5 text-end">
Rp {{ number_format($pengembalian—denda_kerusakan, 8, *,*, '.') }}
</dd>
</d>


=========================================
PAGE 20
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
20

Seluruh angka pada halaman ini berasal dari trigger, tidak satu pun dihitung ulang di PHP. Bila
totalnya salah, yang perlu diperiksa adalah trigger, bukan berkas ini.

5.2.9)
 
Uji seluruh alur

Alur normal, tepat waktu:

1.
 
Sebagai peminjam, buka Pinjaman Saya, tekan Kembalikan pada peminjaman berstatus

dipinjam
.

2.
 
Status berubah menjadi Menunggu Verifikasi, tombol Kembalikan hilang.

3.
 
Sebagai petugas, buka menu Verifikasi. Peminjaman itu muncul di antrian.

4.
 
Catat
 
stok_tersedia
 
alat terkait di phpMyAdmin
 
—
 
tangkapan layar sebelum.

5.
 
Isi kondisi seluruh alat sebagai Baik, denda kerusakan 0, simpan.

6.
 
Halaman rincian tampil: hari terlambat 0, seluruh denda 0.

7.
 
Periksa
 
stok_tersedia
 
—
 
sudah bertambah kembali. Tangkapan layar sesudah.

8.
 
Periksa status peminjaman
 
—
 
selesai
.

Alur terlambat dan rusak:

1.
 
Siapkan peminjaman
 
dipinjam
 
lain, ubah
 
tgl_harus_kembali
 
lewat phpMyAdmin menjadi 3
hari lalu.

2.
 
Sebagai peminjam, tekan Kembalikan.

3.
 
Sebagai petugas, verifikasi: satu alat Baik, satu alat Rusak Berat, denda kerusakan 150000.

4.
 
Halaman rincian harus menampilkan hari terlambat 3, denda keterlambatan sesuai
perhitungan, dan total denda yang sudah termasuk 150000.

5.
 
Periksa tabel
 
alat
: yang berkondisi baik bertambah
 
stok_tersedia
, yang rusak berat
berkurang
 
stok
.

Uji:

1.
 
Sebagai peminjam, tekan Kembalikan hari ini pada peminjaman yang belum lewat tenggat.

2.
 
Sebagai petugas, ubah Tanggal Kembali pada form menjadi 2 hari setelah tenggat, lalu
simpan.

3.
 
Denda harus terhitung, dan di
 
log_aktivitas
 
harus ada baris beraksi
 
koreksi_tgl_kembali
.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_20_img_28.png] ---
<hr>,
<div class="d-flex justify-content-between">
<strong>Total Denda</strong>
<strong class="text-danger fs-5">
Rp {{ number_format($pengembalian—total_denda, 8, *,', *.') }+
</strong>
</div>
@if ($pengembalian—catatan)
<ne>
<div class="small text-muted">{{ $pengembalian—catatan }}</div>
@endif
</div>
</div>


=========================================
PAGE 21
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
21

Uji penjagaan status:

Coba buka alamat verifikasi untuk peminjaman yang statusnya masih
 
dipinjam
 
(belum diajukan
kembali) dengan mengetik alamatnya langsung. Harus muncul HTTP 422.

Halaman Peminjaman Saya tombol Kembalikan :

Halaman Pemantauan :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_21_img_29.png] ---
A PUNEEEENEEEEEEEEEEEEEERR. 0
v © Pinaman Saye x + - oOo x
< CG © localhost:8000/peminjaman/saya Bg (ous) :
Peminjaman Alat Katalog Alat Keranjang Pinjaman Saya Siswa Peminja [ Ketuar |
Pinjaman Saya
Kode Pinjam Tanggal Pinjam Harus Kembali Jumlah Alat Status Aksi
PIM-20260823-003 23/08/2026 30/08/2026 3 [ vipinjam
Kembalikan
PIM-20260823-002 23/08/2026 30/08/2026 1 [ scicsai |
PIM-20260823-001 20/08/2026 21/08/2026 2 [ scicsai |
© Request Timeline Views @ Queries @) Models @ Gate @ Cache @ % 12x B28M8 © 468ms GET /peminjaman/saya B35 5 A X


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_21_img_30.png] ---
| v © Pemantauan Peminjaman x + - oOo x
| <« CG  @ localhost8000/pengembalian/pantau Bx @ :
Peminjaman Alat Persetujuan Pemantauan Verifikas Petugas Laborato [ Ketuar |
| Pemantauan Peminjaman Berjalan
|
Kode Pinjam Peminjam Harus Kembali Jumlah Alat Status
PIM-20260823-003 Siswa Peminjam 30/08/2026 3
©9 Request Timeline Views @ Queries @ Models @) Gate @ Cache @ © 12x B® 28M8 © 483ms GET /pengembalian/pantau 3 = ~ X


=========================================
PAGE 22
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
22

Halaman Verifikasi :

Halaman Form Verikasi :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_22_img_31.png] ---
| v  @ Antrian Verfkasi Pengembalian X + - Oo x

| <« CG  @ localhost8000/pengembalian/antrian Bx @ :
Peminjaman Alat  Persetujuan Pemantauan Verifikas Reinga Lassie =

| Antrian Verifikasi Pengembalian

|

| Kode Pinjam Peminjam Diajukan Kembali Harus Kembali Jumlah Alat Aksi

PIM-20260823-003 Siswa Peminjam 23/08/2026 30/08/2026 3
@9 Request Timeline Views @ Queries @ Models @) Gate @ Cache @ © 12x B28ve © 457ms GET /pengembalian/antrian (3 = ~ X


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_22_img_32.png] ---
v  @ Verfikssi Pengembalsn x + - Oo x
<« CG  @ localhost8000/pengembalian/3/verifikasi Bx @ :
Peminjaman Alat Persetujuan Pemantauan Verifikas Petugas Laborato [ Ketuar |
| Verifikasi PJM-20260823-003 [ Kembali |

Kondisi Alat yang Dikembalikan Data Pengembalian
Kode Nama Alat Jumlah ~~ Kondisi Kembali Peminjam Siswa Peminjam
PKT-003 Kunci Pas Set 1 Rusak Ringan v Harus Kembali 30/05/2026
Diajukan Kembali 23/08/2026
AUK-001 Multimeter Digital 1 Baik v
Perubahan tanggal akan tercatat di log
" ktivitas.
AVI-003 Kamera Mirrorless 1 Baik v sews .
Tanggal Kembali
08/23/2026 [=]
Alat berkondisi baik dan rusak ringan kembali menambah stok tersedia. Alat berkondisi
EE Denda keterlambatan dihitung sistem,
tidak perlu diisi di sini.
Denda Kerusakan
15000
Catatan
p
Simpan Verifikasi
¥s wo@e@® oO =0@ 00 © 12x R29M8 © 544ms GET /pengembalian/3/verifikasi 3 ZF A X


=========================================
PAGE 23
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
23

Halaman Rinci :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_23_img_33.png] ---
~  @ Rincian Pengembalian x + - Oo x
<« CG  @ localhost8000/pengembalian/rincian/3 gr @
Peminjaman Alat Persetujuan Pemantauan Verifikas Petugas Laborato [ Ketuar |
| Pengembalian berhasil diverifikasi.
Rincian PJM-20260823-003 [ Kembali |
Rincian Denda per Alat Ringkasan
Nama Alat Jumlah  Kondisi Denda Peminjam Sowa
Kunci Pas Set 1 rusak ringan RpO Peminjam
Tanggal Kembali 23/08/2026
Multimeter Digital 1 baik RpO )
Hari Terlambat 0 hari
Kamera Mirrorless 1 baik RpO Denda RpO
Keterlambatan
Denda Kerusakan Rp 15.000
Total Denda Rp 15.000
Kunci pas rusak
sup e@® o@ =0 00 % 12x BR 2eMe © 9e2ms @) GET /pengembalian/rincian/3 BB ZF A X
