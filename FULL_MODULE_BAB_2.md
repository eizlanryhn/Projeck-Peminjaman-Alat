
=========================================
PAGE 1
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 1
BAB II
FONDASI PROYEK
2.1 Instalasi Proyek Laravel dan Konfigurasi Basis Data
2.1.1) Perangkat yang Digunakan
• XAMPP atau Laragon yang sudah berjalan (Apache/Nginx + MariaDB/MySQL)
• Composer versi terbaru
• PHP 8.2 atau lebih baru
• Editor kode (VS Code)
• Koneksi internet
2.1.2) Periksa versi PHP
Buka terminal, lalu jalankan :
php -v
composer -V
Pastikan PHP menunjukkan versi 8.2 atau lebih tinggi. Bila masih 8.1 atau di bawahnya, Laravel 12
keatas tidak akan bisa dipasang. Laporkan ke guru sebelum melanjutkan.
2.1.3) Buat proyek baru
Masuk ke folder tempat proyek disimpan, lalu:
composer create-project laravel/laravel peminjaman-alat
cd peminjaman-alat
2.1.4) Pasang paket yang dibutuhkan
Pada terminal, unduh paket yang dibutuhkan :
composer require laravel/fortify spatie/laravel-permission barryvdh/laravel-dompdf
composer require --dev barryvdh/laravel-debugbar
Daftar paket dan alasan pemakainya :
Paket Lingkungan Alasan
laravel/fortify production Menyediakan mesin autentikasi (login,
logout, pembatasan percobaan) tanpa
memaksakan tampilan bawaan
spatie/laravel-permission production Mengelola peran dan izin tiga level
barryvdh/laravel-dompdf production Membuat laporan PDF tanpa perlu Node.js
barryvdh/laravel-debugbar dev Menampilkan jumlah query dan waktu muat
halaman

=========================================
PAGE 2
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 2
2.1.5) Terbitkan berkas konfigurasi paket
Pada terminal, jalankan perintah berikut :
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
Perintah pertama menghasilkan config/fortify.php, folder app/Actions/Fortify/, dan berkas
app/Providers/FortifyServiceProvider.php. Perintah kedua menghasilkan config/permission.php
dan satu migration untuk lima tabel peran dan izin.
2.1.6) Daftarkan FortifyServiceProvider
Buka bootstrap/providers.php dan tambahkan barisnya:
<?php
return [
App\Providers\AppServiceProvider::class,
App\Providers\FortifyServiceProvider::class,
];
Tanpa baris ini, seluruh konfigurasi yang Anda tulis tidak akan pernah dijalankan.
2.1.7) Hapus migration dua faktor
Fortify menerbitkan satu migration yang menambahkan kolom autentikasi dua faktor ke tabel
users. Fitur itu tidak dipakai, dan bila dibiarkan, tabel users tidak akan lagi cocok dengan kamus
data yang Anda susun.
Buka folder database/migrations/, cari berkas berakhiran
_add_two_factor_columns_to_users_table.php, lalu hapus berkas tersebut.
2.1.8) Sesuaikan konfigurasi MariaDB
Buka config/database.php, cari bagian mysql, dan pastikan dua baris berikut bernilai persis seperti
ini:
'charset' => 'utf8mb4',
'collation' => 'utf8mb4_unicode_ci',
Ini wajib. Laravel versi baru kadang memakai nilai bawaan utf8mb4_0900_ai_ci. Nilai itu hanya
dikenal MySQL 8 dan akan membuat MariaDB menolak seluruh migration dengan pesan unknown
collation.
Selanjutnya buka berkas .env dan sesuaikan:
APP_NAME="Peminjaman Alat"
APP_TIMEZONE=Asia/Jakarta

=========================================
PAGE 3
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 3
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peminjaman_alat
DB_USERNAME=root
DB_PASSWORD=
2.1.9) Buat basis data kosong
Buka phpMyAdmin di http://localhost/phpmyadmin, lalu buat basis data baru:
• Nama: peminjaman_alat
• Collation: utf8mb4_unicode_ci
Jangan membuat satu tabel pun secara manual. Seluruh tabel akan dibuat lewat migration.
2.1.10) Uji koneksi
Pada terminal, jalankan perintah ini:
php artisan migrate:fresh
Hasil yang diharapkan: migrasi bawaan Laravel dan migrasi tabel peran dari paket otorisasi
berjalan tanpa kesalahan. Periksa di phpMyAdmin, basis data kini berisi tabel bawaan (users,
sessions, cache, jobs, dan seterusnya) serta lima tabel peran (roles, permissions, model_has_roles,
model_has_permissions, role_has_permissions).
Terakhir, jalankan server pengembangan di terminal:
php artisan serve
Buka http://127.0.0.1:8000. Halaman sambutan Laravel harus tampil, dan di bagian bawah layar
muncul bilah Debugbar.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_3_img_1.png] ---
v  @® Peminjaman Alat x + - Oo x
€ 3 C © localhosts000 (@ Guest) :
Login Register
Lots gestarted he I 0. el
Laravel has an incredibly rich ecosystem. 4
Read the Documentation » NLL £7)
y £
a p
Va D X/ y/A
Na
i
VA. SV E/ A
Ny
\
@9 Request Timeline Views @) Queries @ w 12x B26ME © 404ms GET / BB = A X


=========================================
PAGE 4
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 4
2.2 Migration Delapan Tabel dan Model Eloquent
2.2.1) Sesuaikan tabel users
Tabel users sudah ada sebagai bawaan Laravel, tetapi kolomnya belum sesuai kamus data. Buka
berkas database/migrations/0001_01_01_000000_create_users_table.php dan ubah hanya bagian
Schema::create('users', ...). Dua tabel lain di berkas itu (password_reset_tokens dan sessions)
biarkan apa adanya.
Kolom email boleh kosong dan tetap berindeks unik. MariaDB mengizinkan banyak baris bernilai
NULL pada indeks unik, jadi peminjam yang tidak punya email tidak akan bertabrakan satu sama
lain.
2.2.2) Buat tujuh berkas migration sekaligus
Pada terminal jalankan berurutan, jangan diacak, karena urutan inilah yang menentukan urutan
pembuatan tabel:
php artisan make:migration create_kategori_table
php artisan make:migration create_alat_table
php artisan make:migration create_peminjaman_table
php artisan make:migration create_detail_peminjaman_table
php artisan make:migration create_pengembalian_table
php artisan make:migration create_log_aktivitas_table
php artisan make:migration create_pengaturan_table
Nama berkas migration tetap memakai pola bahasa Inggris create_..._table karena Artisan
memakai pola itu untuk menebak nama tabel secara otomatis. Selebihnya — nama tabel, nama
kolom, nama variabel, dan nama method — seluruhnya berbahasa Indonesia.
2.2.3) Isi migration kategori
Buka file ..._create_kategori_table.php:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_4_img_2.png] ---
Schema ::create('users', function (Blueprint $table) {
$table—id();
$table—string('nama’', 160);
$table—string('username', 50)—unique();
$table—string('email’', 100)—nullable()—unique();
$table— string (‘password’);
$table—string('no_telp', 208)—nullable();
$table—boolean('is_aktif')—default (true);
$table—rememberToken();
$table—timestamps();

b;


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_4_img_3.png] ---
public function up(): void
1
Schema ::create('kategori', function (Blueprint $table) {
$table—id();
$table—string('nama’, 100)—unique();
$table—text('deskripsi')—nullable();
$table—timestamps();
b;
+


=========================================
PAGE 5
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 5
Kolom created_at dan updated_at tidak digambar di ERD karena bukan bagian dari model bisnis,
tetapi tetap dibuat agar fitur bawaan Eloquent berjalan normal.
2.2.4) Isi migration alat
Berkas ini adalah yang pertama memuat foreign key dan CHECK constraint. Jangan lupa
menambahkan use Illuminate\Support\Facades\DB; di bagian atas berkas.
Buka file …_create_alat_table.php :
Tiga hal yang perlu dipahami dari kode di atas:
Bagian Alasan
constrained('kategori') Nama tabel ditulis eksplisit. Tanpa itu Laravel menebak tabelnya
bernama kategoris, dan migration gagal
restrictOnDelete() Kategori yang masih dipakai alat tidak dapat dihapus.
DB::statement untuk CHECK Schema Builder Laravel tidak menyediakan method untuk CHECK
constraint, jadi ditulis sebagai SQL mentah

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_5_img_4.png] ---
public function down(): void
1

Schema ::dropIfExists('kategori');
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_5_img_5.png] ---
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
public function up(): void
{
Schema::create('alat', function (Blueprint $table) {
$table—id();
$table—foreignId('kategori_id')
—constrained('kategori')
—restrictOnDelete();
$table—string('kode_alat', 38)—unique();
$table—string('nama', 150);
$table—text('deskripsi')—nullable();
$table—integer('stok')—default(e);
$table—integer(*stok_tersedia*)—default (0);
$table—enum('kondisi’, ['baik', ‘rusak_ringan‘, ‘rusak_berat'])
—default(baik');
$table—string(*foto')—nullable();
$table—timestamps();
BH;
DB:: statement ('ALTER TABLE alat
ADD CONSTRAINT chk_alat_stok CHECK (stok > 8)');
DB :: statement ('ALTER TABLE alat
ADD CONSTRAINT chk_alat_tersedia
CHECK (stok_tersedia > © AND stok_tersedia < stok)');
+
public function down(): void
{
Schema :: dropIfExists(‘alat');
+
53


=========================================
PAGE 6
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 6
CHECK aturan 0 <= stok_tersedia <= stok dijaga langsung oleh basis data, sehingga tetap berlaku
walaupun ada kesalahan di kode PHP.
2.2.5) Isi migration peminjaman
Buka file …._create_peminjaman_table.php:
Tabel ini punya dua foreign key ke tabel yang sama. user_id adalah peminjamnya, petugas_id
adalah petugas yang memproses. petugas_id boleh kosong karena pengajuan yang baru masuk
belum diproses siapa pun.
Kolom status diberi ->index() karena hampir setiap halaman daftar menyaring berdasarkan kolom
ini.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_6_img_6.png] ---
public function up(): void
{

Schema::create(*peminjaman', function (Blueprint $table) {
$table—id();
$table—string('kode_pinjam', 20)—unique();
$table—foreignId('user_id')

—constrained(‘users*)

—restrictOnDelete();
$table—foreignId('petugas_id')

—nullable()

—constrained(‘users*)

—nullonDelete();
$table—date('tgl_pinjam');
$table—date("tgl_harus_kembali');
$table—date(* tgl_diajukan_kembali')—nullable();
$table—enun('status', [

*diajukan’,

‘ditolak’,

*dipinjan’,

*menunggu_verifikasi',

‘selesai’,
1)—default('diajukan')—index();
$table—text('keperluan')—nullable();
$table—text('alasan_tolak')—>nullable();
$table— timestamps ();

BH;

+
public function down(): void
{
Schema: dropIfExists(*peminjaman');
+


=========================================
PAGE 7
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 7
2.2.6) Isi migration detail_peminjaman
Buka file …._create_detail_peminjaman_table.php, dan tambahkan use
Illuminate\Support\Facades\DB:
Dua pilihan penting di sini:
• cascadeOnDelete() ke peminjaman — bila data induk dihapus, baris detailnya ikut terhapus.
Ini aman karena hanya mengizinkan penghapusan pengajuan yang belum pernah disetujui.
• restrictOnDelete() ke alat — alat yang pernah dipinjam tidak boleh dihapus, karena
riwayat transaksi harus tetap utuh.
• unique(['peminjaman_id', 'alat_id']) — Satu alat hanya boleh muncul satu kali dalam satu
pengajuan.
Nilai kondisi_kembali sengaja kosong sampai alat benar-benar dikembalikan. Nilai kosong inilah
yang dibaca untuk memutuskan alat mana yang kembali ke stok.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_7_img_7.png] ---
public function up(): void
{

Schema:: create (*detail_peminjaman', function (Blueprint $table) {
$table—id();
$table—foreignId('peminjaman_id')

—>constrained(*peminjaman’)

—cascadeOnDelete();
$table—foreignId('alat_id')

—constrained(‘alat')

—restrictOnDelete();
$table—integer(*jumlan');
$table—enun('kondisi_kembali', [

*baik’,

*rusak_ringan®,

*rusak_berat”,

*nitlang’,
1)-nullable();
$table—decimal('denda’, 12, 2)—default(e);
$table— timestamps ();
$table—unique(['peminjaman_id', 'alat_id']);

BH;

DB:: statement ("ALTER TABLE detail _peminjaman

ADD CONSTRAINT chk_detail_jumlah CHECK (jumlah > 8)');

+
public function down(): void
{
Schema :: dropIfExists(*detail_peminjaman');
+


=========================================
PAGE 8
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 8
2.2.7) Isi migration pengembalian
Buka file …._create_pengembalian_table.php :
peminjaman_id diberi ->unique() karena relasinya satu ke satu: satu pengajuan hanya boleh
menghasilkan satu baris pengembalian. Tanpa indeks unik ini, petugas yang tidak sengaja menekan
tombol verifikasi dua kali akan membuat denda terhitung dua kali.
Tiga kolom denda semuanya bernilai bawaan nol, karena isinya akan ditulis oleh trigger, bukan
diketik manusia.
2.2.8) Isi migration log_aktivitas dan pengaturan
Buka file …_create_log_aktivitas_table.php :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_8_img_8.png] ---
public function up(): void
{
Schema::create(*pengembalian’, function (Blueprint $table) {
$table—id();
$table—foreignId('peminjaman_id')

Sunique()

—>constrained(*peminjaman®)

—restrictOnDelete();
$table—foreignId('petugas_id')

—constrained(‘users*)

—restrictOnDelete();
$table—date('tgl_kembali');
$table—integer(*hari_terlambat')—>default (6);
$table—decimal('denda’, 12, 2)—default(e);
$table—decinal('denda_kerusakan', 12, 2)—default(e);
$table—decinal (total _denda’, 12, 2)—default(e);
$table—text('catatan’)—nullable();
$table— timestamps ();

BH;
+
public function down(): void
{
Schema :: dropIfExists(‘pengembalian*);
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_8_img_9.png] ---
public function up(): void
{
Schema::create(*log_aktivitas', function (Blueprint $table) {
$table—id();
$table—foreignId('user_id')
—nullable()
—constrained(‘users*)
—nullonDelete();
$table—string('aksi', 50);
$table—string('tabel_tujuan’, 56)—nullable();
$table—text('deskripsi')—nullable();
$table—string(*ip_address’, 45)—nullable();
$table— timestamp ('created_at')—useCurrent()—index();
BH;
+
public function down(): void
{
Schema: dropIfExists('log_aktivitas');
+


=========================================
PAGE 9
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 9
Tabel ini hanya punya created_at, tanpa updated_at, karena catatan log tidak pernah diubah. Kolom
user_id boleh kosong supaya percobaan login yang gagal — yang pelakunya belum diketahui —
tetap dapat dicatat.
Buka file …._create_pengaturan_table.php:
2.2.9) Jalankan migration
Pada terminal jalan migration :
php artisan migrate:fresh
Perintah migrate:fresh menghapus seluruh tabel lalu membuatnya ulang dari awal. Aman dipakai
sekarang karena belum ada satu pun data penting.
Buka phpMyAdmin dan periksa: harus ada 8 tabel domain, 5 tabel peran dan izin, serta tabel
infrastruktur bawaan Laravel.
Uji juga bahwa CHECK constraint benar-benar bekerja. Jalankan perintah berikut di tab SQL
phpMyAdmin:
INSERT INTO kategori (nama, created_at, updated_at)
VALUES ('Uji Coba', NOW(), NOW());
INSERT INTO alat (kategori_id, kode_alat, nama, stok, stok_tersedia, kondisi, created_at,
updated_at)
VALUES (1, 'UJI-001', 'Alat Uji', 5, 9, 'baik', NOW(), NOW());
Perintah kedua harus gagal, karena stok_tersedia bernilai 9 sementara stok hanya 5. Bila justru
berhasil, CHECK constraint Anda tidak terpasang; periksa kembali Langkah 4. Setelah terbukti,
jalankan lagi php artisan migrate:fresh untuk membersihkan data uji.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_9_img_10.png] ---
public function up(): void
{
Schema::create(*pengaturan’, function (Blueprint $table) {
$table—id();
$table—string('kunci', 58)—unique();
$table—string('nilai’, 255);
$table—timestamps();
BH;
+
public function down(): void
{
Schema :: dropIfExists(*pengaturan’);
+


=========================================
PAGE 10
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 10
2.2.10) Buat model Eloquent
Pada terminal buat model :
php artisan make:model Kategori
php artisan make:model Alat
php artisan make:model Peminjaman
php artisan make:model DetailPeminjaman
php artisan make:model Pengembalian
php artisan make:model LogAktivitas
php artisan make:model Pengaturan
Laravel menebak nama tabel dengan menambahkan huruf s pada nama model. Karena seluruh
tabel kita berbahasa Indonesia dan tidak berbentuk jamak, setiap model wajib menyebut nama
tabelnya secara eksplisit.
Buka file app\Models\Kategori.php :
Buka file app\Models\Alat.php :
Buka file app\Models\Peminjaman.php :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_10_img_11.png] ---
class Kategori extends Model
{

protected $table = 'kategori';

protected $fillable = ['nama’, 'deskripsi'l;
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_10_img_12.png] ---
class Alat extends Model
{
protected $table = 'alat’;
protected $fillable = [
'kategori_id', 'kode_alat®, ‘mama’, 'deskripsi‘,
*stok*, ‘stok_tersedia’, 'kondisi', ‘foto’,
JI
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_10_img_13.png] ---
use Illuminate\Database\Eloguent\Model;
class Peminjaman extends Model
{
protected $table = ‘peminjaman';
protected $fillable = [
*kode_pinjam', ‘user_id’, 'petugas_id’, 'tgl_pinjam',
*tgl_harus_kembali', 'tgl_diajukan_kembali', ‘status’,
*keperluan®, ‘alasan_tolak’,
JI
protected $casts = [
*tgl_pinjam' = ‘date’,
*tgl_harus_kembali' = ‘date’,
*tgl_diajukan_Kkembali' = ‘date’,
JI
}


=========================================
PAGE 11
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 11
Buka file app\Models\DetailPeminjaman.php :
Buka file app\Models\Pengembalian.php :
Buka file app\Models\LogAktivitas.php :
Buka file app\Models\Pengaturan.php :
public $timestamps = false pada LogAktivitas wajib ada. Tanpa itu Eloquent akan mencari kolom
updated_at yang memang sengaja tidak dibuat, dan setiap penulisan log akan gagal.
Method ambil() pada Pengaturan akan dipakai berkali-kali. Nilainya sengaja dibaca langsung dari
basis data tanpa cache, supaya selalu sama dengan nilai yang dibaca trigger.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_14.png] ---
class DetailPeminjaman extends Model
h protected $table = ‘detail peminjaman';
protected $fillable = [
‘peminjaman_id’, ‘alat_id®, 'jumlah', 'kondisi_kembali', 'denda’,
\ Jil


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_15.png] ---
class Pengembalian extends Model
{
protected $table = ‘pengembalian’;
protected $fillable = [
*peminjaman_id', ‘petugas_id’, 'tgl_kembali’,
*denda_kerusakan®, ‘catatan’,
JI
protected $casts = [
*tgl_kembali’ = ‘date’,
JI
}


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_16.png] ---
class LogAktivitas extends Model
h protected $table = 'log_aktivitas';
public $timestamps = false;
protected $fillable = [
‘user_id*, ‘aksi', ‘tabel_tujuan', ‘deskripsi‘, ‘ip_address’,
\ Jil


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_17.png] ---
Class Pengaturan extends Model
{
protected $table = 'pengaturan’;
protected $fillable = ['kunci’, *nilai'l;
public static function ambil(string $kunci, $bawaan = null)
{
$baris = static::where('kunci', $kunci)—First();
return $baris ? $baris—nilai : $bawaan;
+
+


=========================================
PAGE 12
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 12
Relasi antar model belum ditulis sekarang. Masing-masing akan ditambahkan pada job sheet yang
membutuhkannya.

=========================================
PAGE 13
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 13
2.3 Peran, Izin, dan Seeder Data Awal
2.3.1) Siapkan model User
Buka file app\Models\User.php dan tambahkan trait dari paket otorisasi, lalu sesuaikan kolom yang
boleh diisi:
Trait HasRoles memberi model ini method assignRole(), hasRole(), dan can() yang akan dipakai di
hampir seluruh job sheet berikutnya.
Cast 'password' => 'hashed' membuat Laravel otomatis mengubah password menjadi hash bcrypt
setiap kali kolom itu diisi. Karena itu, mulai sekarang jangan pernah menulis Hash::make() secara
manual — password akan ter-hash dua kali dan login selalu gagal.
2.3.2) Daftarkan alias middleware
Buka bootstrap/app.php dan lengkapi bagian withMiddleware:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_13_img_18.png] ---
"use Illuminate\Database\Eloguent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{

use HasFactory, Notifiable, HasRoles;
: protected $fillable = [
‘nama’, ‘username’, ‘email’, ‘password’, 'no_telp’, 'is_aktif',
JI
: protected $hidden = [
*password®,
*remenber_token",
JI
: protected function casts(): array
{
return [
'is_aktif' = ‘boolean’,
‘password’ = ‘hashed’,
1
+
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_13_img_19.png] ---
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\NMiddleware\RoleOrPernissiontiddleware;
return Application::configure(basePath: dirname(_DIR_))
—withRouting(
web: __DIR_.'/../routes/web.php’,
commands: __DIR__.'/../routes/console.php*,
health: */up*,
)
—withMiddleware (function (Middleware $middleware): void {
$middleware—atias([
‘role’ = RoleMiddleware::class,
*permission® =PermissionMiddleware ::class,
*role_or_permission® => RoleOrPermissionMiddleware :: class,
n;
Bb
—withExceptions (function (Exceptions $exceptions): void {
HF) -create();


=========================================
PAGE 14
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 14
Tanpa pendaftaran ini, penulisan middleware('permission:alat.kelola') pada route akan
menghasilkan kesalahan target class does not exist.
2.3.3) Buat seeder peran dan izin
Buka terminal buat seeder :
php artisan make:seeder PeranIzinSeeder
Isi file database/seeders/PeranIzinSeeder.php:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_14_img_20.png] ---
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Pernission;
use Spatie\Permission\Models\Role;
class PeranIzinSeeder extends Seeder
vi
public function run(): void
~ {
v $daftarlzin = [
/ Izin milik Admin
‘user.kelola',
‘alat.kelola’,
*kategori.kelola®,
*peminjaman.kelola’,
*pengembalian.kelola’,
“log. Lihat’,
*pengaturan.kelola’,
/ Izin milik Petugas
*peminjaman.setujui®,
*pengembalian.pantau®,
*laporan.cetak’,
/ Izin milik Peminjam
‘alat.linat’,
*peminjaman.ajukan’
*peminjaman.kembalikan®,
1
~ foreach ($daftarIzin as $namalzin) {
~v Permission :: firstorCreate([
name" = $namalzin,
*guard_name' = ‘web’,
n;
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_14_img_21.png] ---
$peranAdmin = Role::firstOrCreate(['name’ = ‘admin', ‘guard_name' = ‘web']);
$peranPetugas = Role::firstOrCreate(['name' => 'petugas', 'guard_name' = 'web']);
$peranPeminjam = Role::firstOrCreate(['name' => 'peminjam', 'guard_name' => ‘'web']);
$peranAdmin—rsyncPermissions ([

‘user.kelola',

‘alat.kelola’,

*kategori.kelola®,

*peminjaman.kelola’,

*pengembalian.kelola’,

“log. Lihat’,

*pengaturan.kelola’,
n;
$peranPetugas—syncPermissions([

*peminjaman.setujui®,

*pengembalian.pantau®,

*laporan.cetak’,
n;


=========================================
PAGE 15
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 15
Perhatikan pemetaannya terhadap matriks hak akses. Tidak ada satu pun izin yang dimiliki dua
peran sekaligus. Admin tidak memiliki peminjaman.setujui, dan Petugas tidak memiliki
alat.kelola.
firstOrCreate dan syncPermissions dipakai supaya seeder aman dijalankan berulang kali.
syncPermissions mengganti seluruh daftar izin peran dengan daftar baru, jadi bila nanti ada izin
yang dihapus dari daftar, izin itu ikut terlepas dari perannya.
2.3.4) Buat seeder pengguna
Buka terminal buat seeder :
php artisan make:seeder PenggunaSeeder
Buka file database\seeders\PenggunaSeeder.php :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_15_img_22.png] ---
$peranPeminjan—syncPernissions ([
‘alat.lihat®,
*peninjaman.ajukan* ,
*peminjaman.kembalikan',
n;
}
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_15_img_23.png] ---
use App\Models\User;
use Illuminate\Database\Seeder;
class PenggunaSeeder extends Seeder
{
public function run(): void
{
$daftarPengguna = [
r
*nama* = ‘Administrator’,
‘username’ = ‘admin’,
‘email’ = ‘admin@sekolah.sch.id’,
‘no_telp' => '881200800601°,
‘pean’ => ‘admin’,
in
r
*nama* = 'Petugas Laboratorium’,
‘username’ = ‘petugas’,
‘email’ = ‘petugas@sekolah.sch.id’,
‘no_telp' => '881200800002°,
‘peran’ = ‘petugas’,
in
r
*nama* = 'siswa Peminjan',
‘username’ = ‘peminjan’,
‘email’ = ‘peminjam@sekolah.sch.id’,
‘no_telp' => '881200800003",
‘pean’ = ‘peminjam’,
in
IF]


=========================================
PAGE 16
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 16
Password ditulis polos di seeder, dan itu tidak masalah: cast 'password' => 'hashed' pada model
User mengubahnya menjadi hash bcrypt sebelum disimpan. Buktikan nanti dengan melihat kolom
password di phpMyAdmin.
2.3.5) Buat seeder pengaturan, kategori, dan alat
Buka terminal ketikan perintah membuat seeder :
php artisan make:seeder PengaturanSeeder
php artisan make:seeder KategoriSeeder
php artisan make:seeder AlatSeeder
Buka file database\seeders\PengaturanSeeder.php :
Ganti nilai nama_sekolah dengan nama sekolah Anda sendiri, karena nilai itu akan tercetak sebagai
kop pada seluruh laporan PDF.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_16_img_24.png] ---
foreach ($daftarPengguna as $data) {
$pengguna = User ::firstOrCreate(
['username’ = $datal'username’]],
[
*nama’ = $datal'nama'],
‘email’ = $datal'email'],
'no_telp' = $datal'no_telp'],
‘password’ => ‘password123',
‘is_aktif' = true,
1
pH
$pengguna—syncRoles([$datal peran']]);
+
+
}


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_16_img_25.png] ---
class Pengaturanseeder extends Seeder
vi
public function run(): void
~ {
~ $daftarPengaturan = [
['kunci' =» 'tarif_denda_harian', ‘nilai' => '5600'],
["kunci' = ‘default_hari_pinjam', ‘nilai' = '7'],
['kunci' = 'maks_hari_pinjam',  ‘nilai’ = '36'],
['kunci' => 'nama_sekolah', 'nilai' = 'SMK Negeri 1 Contoh'],
1
v foreach ($daftarPengaturan as $data) {
“ Pengaturan :: firstorCreate(
['kunci* = $datal'kunci'l],
[*nilai* = $datal’'nitai']]
3
+
+
}


=========================================
PAGE 17
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 17
Buka file database\seeders\KategoriSeeder.php :
Buka file database\seeders\AlatSeeder.php :
Saat pertama kali dibuat, stok_tersedia selalu sama dengan stok, karena belum ada satu unit pun
yang dipinjam.
Jangan lupa menambahkan pernyataan use di bagian atas tiap berkas seeder, misalnya use
App\Models\Kategori; dan use App\Models\Alat;.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_17_img_26.png] ---
class KategoriSeeder extends Seeder
{
public function run(): void
{
$daftarKategori = [
[*nama’ = ‘Perkakas Tangan', ‘deskripsi' => 'Obeng, tang, kunci, palu'l,
[*nama’ = ‘Alat Ukur®, ‘deskripsi’ = ‘Multimeter, jangka sorong, mistar baja'l,
[*nama’ = ‘Perangkat Jaringan','deskripsi’ => ‘Switch, router, tang crimping],
[*nama’ = ‘Perangkat Audio Visual’, 'deskripsi’ => 'Proyektor, kamera, tripod'],
1
foreach ($daftarKategori as $data) {
Kategori::firstOrCreate(['nama' = $datal'nama']], $data);
+
+
}


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_17_img_27.png] ---
class AlatSeeder extends Seeder
{
public function run(): void
{
$daftaratlat = [
['kode_alat' => 'PKT-801', 'nama' => 'Obeng Plus Set', 'kategori' =» 'Perkakas Tangan', 'stok' = 18],
['kode_alat' => 'PKT-802', 'nama'’ => 'Tang Kombinasi', 'kategori' =» 'Perkakas Tangan', 'stok' = 8],
['kode_alat' => 'PKT-803', 'nama’ => 'Kunci Pas Set’, 'kategori' =» 'Perkakas Tangan', 'stok' = 6],
['kode_alat' => 'AUK-801', 'nama’ => 'Multimeter Digital’, 'kategori' = 'Alat Ukur', 'stok' = 12],
['kode_alat' => 'AUK-802', 'nama’ => 'Jangka Sorong', 'kategori' = 'Alat Ukur', 'stok' = 9],
['kode_alat' => 'AUK-803', 'nama’ => 'Mistar Baja 30 cm', 'kategori' = 'Alat Ukur', 'stok' = 15],
['kode_alat' => 'JAR-801', 'nama’ => 'Tang Crimping RJ45', 'kategori' =» 'Perangkat Jaringan', 'stok' = 10],
['kode_alat' => 'JAR-002', 'nama' => 'LAN Tester’, 'kategori' =» 'Perangkat Jaringan', 'stok' = 5],
['kode_alat' => 'JAR-803', 'nama’ => 'Switch 8 Port’, 'kategori' =» 'Perangkat Jaringan', 'stok' = 4],
['kode_alat' => 'AVI-001', 'nama’ => 'Proyektor Portabel', 'kategori' =» 'Perangkat Audio Visual', 'stok' = 3],
['kode_alat' => 'AVI-002', 'nama’ => 'Tripod Kamera', 'kategori' =» 'Perangkat Audio Visual', 'stok' = 6],
['kode_alat' => 'AVI-003', 'nama’ => 'Kamera Mirrorless', 'kategori' =» 'Perangkat Audio Visual', 'stok' = 2],
1
foreach ($daftarAlat as $data) {
$kategori = Kategori::where('nama', $datal'kategori'l)—first();
Alat :: firstorCreate(
['kode_alat' = $datal'kode_alat']],
[
'kategori_id' = $kategori—did,
*nama’ = $datal'nama'],
*stok" = $datal'stok'],
‘stok_tersedia’ => $data['stok'],
*kondisit = ‘baik’,
1
pH
+
+
}


=========================================
PAGE 18
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 18
2.3.6) Atur urutan pemanggilan seeder
Buka file database/seeders/DatabaseSeeder.php:
Urutannya tidak boleh diacak. PenggunaSeeder memanggil syncRoles(), jadi perannya harus sudah
ada. AlatSeeder mencari kategori berdasarkan nama, jadi kategorinya harus sudah ada.
Seeder untuk transaksi contoh belum dibuat sekarang, karena tabel peminjaman baru akan diisi
lewat antarmuka.
2.3.7) Jalankan dan periksa
Pada terminal jalan migrate serta seednya dan reset cache permission :
php artisan migrate:fresh --seed
php artisan permission:cache-reset
Perintah kedua wajib dijalankan setiap kali daftar izin berubah. Paket otorisasi menyimpan daftar
izin di cache selama 24 jam, dan tanpa penyegaran, izin baru Anda tidak akan terbaca sampai
besok.
Periksa di phpMyAdmin:
Tabel Jumlah baris yang diharapkan
permissions 13
roles 3
role_has_permissions 13
model_has_roles 3
users 3
pengaturan 4
kategori 4
alat 12

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_18_img_28.png] ---
"use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{

public function run(): void
{
$this—call(l
PeranIzinseeder ::class,
Penggunaseeder :: class,
Pengaturanseeder :: class,
KategoriSeeder:: class,
AlatSeeder::class,
n;
+
}


=========================================
PAGE 19
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 19
2.4 Autentikasi Fortify, Layout, dan Dasbor Tiga Peran
2.4.1) Matikan fitur Fortify yang tidak dipakai
Buka config/fortify.php dan sesuaikan empat bagian berikut:
'guard' => 'web',
'username' => 'username',
'home' => '/dasbor',
'features' => [
// Seluruh fitur bawaan dimatikan.
// Login dan logout tetap tersedia karena bukan bagian dari daftar ini.
],
Isian 'features' => [] mematikan registrasi mandiri, reset password, verifikasi email, autentikasi
dua faktor, dan pembaruan profil. Semuanya di luar ruang lingkup, dan mematikannya sekarang
mencegah munculnya halaman yang tidak pernah Anda buat tampilannya.
2.4.2) Tulis logika autentikasi
Buka app/Providers/FortifyServiceProvider.php. Ganti seluruh isinya:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_19_img_29.png] ---
use App\Http\Responses\LoginResponse;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
, use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse as KontrakLoginResponse;
use Laravel\Fortify\Fortify;
class FortifyServiceProvider extends ServiceProvider
{
: public function register(): void
{
$this—app—singleton(KontrakLoginResponse :: class, LoginResponse :: class);
+
: public function boot(): void
{
Fortify :: username (‘username’);
Fortify ::loginview(fn () = view('auth.login'));
Fortify::authenticateUsing(function (Request $request) {
return $this—periksakredensial($request);
BH;
RateLimiter::for('login', function (Request $request) {
/ Karena Anda menggunakan ‘username’ untuk login:
$identifier = (string) $request—username;
/ Batasi 5 kali percobaan gagal per menit berdasarkan username dan IP Address
return Limit::perMinute(5)—by($identifier . $request—ip());
BH;
+


=========================================
PAGE 20
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 20
Bagian yang paling sering salah: cabang akun nonaktif harus melempar ValidationException,
bukan sekadar mengembalikan null. Bila hanya mengembalikan null, Fortify menampilkan pesan
gagal generik.
Perhatikan juga bahwa catatLoginGagal() mengisi user_id dengan null pada cabang pertama. Inilah
alasan kolom itu dibuat boleh kosong: saat username-nya tidak dikenal, memang tidak ada user
yang bisa dicatat.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_20_img_30.png] ---
private function periksaKredensial(Request $request): User
{
$pengguna = User::where('username’, $request—username)>first();
if (1 $pengguna || ! Hash::check($request—password, $pengguna—password)) {
$this—catatLoginGagal($request);
return null;
+
if (1 $pengguna—is_aktif) { - .
$this—catatLoginGagal($request, $pengguna—id);
throw ValidationException ::withMessages ([
‘username’ => ‘Akun Anda dinonaktifkan. Hubungi administrator.’
n;
+
Cabang 3: kredensial cocok dan akun aktif
LogAktivitas::create([
‘user_id* = $pengguna—id,
‘aksi’ = ‘login’,
‘tabel_tujuan' = ‘users’,
'deskripsi’ = 'Pengguna ' . $pengguna—username . ' berhasil masuk’',
*ip_address® = $request—ip(),
n;
return $pengguna;
}


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_20_img_31.png] ---
private function catatLoginGagal(Request $request, ?int $penggunald = null): void
{
LogAktivitas::create([
‘user_id" = $penggunald,
‘aksi’ = 'login_gagal’,
‘tabel_tujuan' = ‘users’,
'deskripsi’ = 'Percobaan masuk gagal untuk username ' . $request—username,
*ip_address’ => $request—ip(),
n;
+
+


=========================================
PAGE 21
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 21
2.4.3) Buat penentu tujuan setelah login
Buat berkas baru app/Http/Responses/LoginResponse.php:
2.4.4) Susun route
Buka routes/web.php:
Route login dan logout tidak perlu ditulis. Keduanya sudah didaftarkan Fortify secara otomatis.
Ketiga dasbor untuk sementara masih berupa halaman kosong. Isinya akan bertambah seiring
modul-modul berikutnya selesai.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_21_img_32.png] ---
namespace App\Http\Responses;
use Laravel\Fortify\Contracts\LoginResponse as KontrakLoginResponse;
class LoginResponse implements KontrakLoginResponse
{
public function toResponse($request)
{
$pengguna = $request—user();
if ($pengguna—shasRole('admin')) {
return redirect()—route('admin.dasbor');
+
if ($pengguna—hasRole('petugas')) {
return redirect()—>route('petugas.dasbor*);
+
return redirect()—route('peminjam.dasbor');
+
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_21_img_33.png] ---
use Illuminate\Support\Facades\Route;
Route::get(*/*, function () {
return redirect()—route('login');
BH;
Route ::middleware(['auth'])—group (function () {
Route::get(*/admin/dasbor*, function () {
return view('dasbor.admin');
})— middleware ('role:admin')—name('admin.dasbor');
Route::get(*/petugas/dasbor®, function () {
return view('dasbor.petugas');
})—middleware('role:petugas’)—name('petugas.dasbor');
Route::get(*/peminjan/dasbor*, function () {
return view('dasbor.peminjam');
})—middleware('role:peminjam*)—name(‘peminjam.dasbor');
BH;


=========================================
PAGE 22
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 22
2.4.5) Buat layout utama
Buat file resources/views/layouts/utama.blade.php:
Buat file resources/views/layouts/navbar.blade.php:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_22_img_34.png] ---
<!DOCTYPE html>
<ntml lang="id">
+ <head>
<meta charset="utf-g8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>gyield(*judul’, 'Peminjaman Alat')</title>
<link href="nttps: //cdn.jsdelivr.net/npn/bootstrap@s.3.3/dist/css/bootstrap.min.css” rel="stylesheet">
</head>
+ <body class="bg-Light">
@include(*layouts.navbar*)
v <div class="container py-4">
v @if (session('sukses'))
<div class="alert alert-success">{{ session('sukses') }}</div>
endif
v @if (session('gagal’))
<div class="alert alert-danger*>{{ session('gagal') } </div>
endif
@yield('konten')
</div>
<script src="https: //cdn.jsdelivr.net/npn/bootstrap@s.3.3/dist/js/bootstrap.bundle.min. js*></script>
</body>
</html>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_22_img_35.png] ---
v <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
v <div class="container">
<a class="navbar-brand" href="{{ url('/') }}">Peminjaman Alat</a>
“ <button class="navbar-toggler” type="button” data-bs-toggle="collapse” data-bs-target="#menuUtama">
<span class="navbar-toggler-icon"></span>
</button>
“ <div class="collapse navbar-collapse” id="menuUtama">
“ <ul class="navbar-nav me-auto">
“ @can(*kategori.kelola')
<li class="nav-iten"><a class="nav-link" href="#">Kategori</a></1i>
@endcan
v @can('alat.kelola')
<li class="nav-iten"><a class="nav-link" href="#">Alat</a></li>
@endcan
v @can('user.kelola')
<li class="nav-iten"><a class="nav-link" href="#">Pengguna</a></li>
@endcan
“ @can(*peminjaman. setujui’)
<li class="nav-iten"><a class="nav-link" href="#">Persetujuan</a></li>
@endcan
v @can('alat.lihat')
<li class="nav-iten"><a class="nav-link" href="#">Katalog Alat</a></li>
@endcan
</ul>


=========================================
PAGE 23
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 23
Menu di layout ini dibungkus @can, bukan @if yang membandingkan nama peran. Akibatnya, menu
otomatis menyesuaikan izin yang Anda seed — bila izin berubah, menu ikut berubah tanpa
menyentuh berkas ini.
Tautan menu masih berisi tanda pagar. Alamat sebenarnya diisi pada job sheet yang membuat
modulnya.
2.4.6) Buat halaman login
Buat file resources/views/auth/login.blade.php:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_23_img_36.png] ---
v @auth
~ <ul class="navbar-nav">
“ <li class="nav-iten">
<span class="navbar-text me-3">{{ auth()—user()—nama }}</span>
</li>
“ <li class="nav-iten">
“ <form method="POST* action="{{ route('logout') }}">
@csrf
<button type="submit" class="btn btn-sm btn-outline-light">Keluar</button>
</form>.
</li>
</ul>
@endauth
</div>
</div>
</nav>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_23_img_37.png] ---
@extends('layouts.utama*)
@section(*judul', ‘Masuk')
@section('konten')
<div class="row justify-content-center">
<div class="col-md-5">
<div class="card shadow-sm">
<div class="card-body p-4">
<h4 class="card-title mb-4 text-center"-Masuk ke Sistem</h4>
<form method="POST" action="{{ route('login‘) }H">
@csrf
<x-input name="username" label="Nama Pengguna” required autofocus />
<x-input name="password” label="Kata Sandi" type="password" required value="" />
<button type="submit" class="btn btn-primary w-106">Masuk</button>
</form>
</div>
</div>
</div>
</div>
@endsection
Era |


=========================================
PAGE 24
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 24
Buat file resources\views\components\input.blade.php :
2.4.7) Buat tiga halaman dasbor
Buat resources/views/dasbor/admin.blade.php:
Buat dua berkas serupa dengan mengganti judulnya: dasbor/petugas.blade.php dan
dasbor/peminjam.blade.php.
2.4.8) Uji seluruh cabang login
Jalankan php artisan serve, lalu buka http://127.0.0.1:8000.
Percobaan Masukan Hasil yang harus muncul
1 admin / password123 Masuk ke Dasbor Admin, menu berisi Kategori, Alat,
Pengguna
2 petugas / password123 Masuk ke Dasbor Petugas, menu hanya berisi
Persetujuan
3 peminjam / password123 Masuk ke Dasbor Peminjam, menu hanya berisi
Katalog Alat
4 admin / salah Tetap di halaman login, muncul pesan kredensial tidak
cocok
5 Akun nonaktif Muncul pesan “Akun Anda dinonaktifkan”
6 Salah password 6 kali
beruntun
Ditolak sementara oleh pembatasan percobaan Fortify

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_24_img_38.png] ---
@props([*name*, 'label', 'type' = ‘text’, 'value' = ""])
<div class="mb-3">
<label for="{{ $name }}" class="form-label">{{ $label } </label>
<input type="{{ $type }}"
class="form-control @error($name) is-invalid @enderror"
id="{{ $name }"
name="{{ $name }"
value="{{ old($name, $value) }}*
{{ $attributes H+
>
@error ($name)
<div class="invalid-feedback">
{{ $message 1+
</div>
@enderror
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_24_img_39.png] ---
@extends('layouts.utama*)
@section(*judul', ‘Dasbor Admin’)
@section('konten')
<hé4>Dasbor Admin</hé4>
<p class="text-muted">Selamat datang, {{ auth()—user()—nama }}.</p>
@endsection


=========================================
PAGE 25
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 25
Halaman Login :
Halaman dasbor admin :
Halaman dasbor petugas :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_25_img_40.png] ---
VQ Masuk x + = o Xx
¢€ > CGC © locahost8000/login Bg (ous) :
Peminjaman Alat
Masuk ke Sistem
Nama Pengguna
Kata Sandi
@9 Request Timeline Views @ Queries @ Gate @ % 12x B26ME © 914ms @P CET /login BB = A X


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_25_img_41.png] ---
~  @ Dasbor Admin x + = o Xx
« CG @ localhost:8000/admin/dasbor Bg (ous) :
Peminjaman Alat Kategori Alat Pengguna A strato [ Ketuar |

Dasbor Admin
Selamat datang, Administrator.
Ss wb 0 90 =0 20 ® 12x B28Me @ 4ssms @ GET /admin/dasbor B ZF A X


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_25_img_42.png] ---
v  @ DasborPetugas x + - Oo x
« CG @ localhost:8000/petugas/dasbor Bg (ous) :
Peminjaman Alat  Persetujua Petugas Laborato [ Keluar |

Dasbor Petugas
Selamat datang, Petugas Laboratorium.
Ss wb 0 90 =0 20 @ 12x B 288 © 57sms @ GET /petugas/dasbor © ZF A X


=========================================
PAGE 26
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT 26
Halaman dasbor peminjam :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_26_img_43.png] ---
v  @ Dasbor Peminjam x + - Oo x
« CG  @ localhost:8000/peminjam/dasbor Bg (ous) :
Peminjaman Alat Katalog Alat Siswa Peminja [ fam. J

Dasbor Peminjam
Selamat datang, Siswa Peminjam.
Ss wb 0 90 =0 20 @ 12x B28MB © 475ms @ GET /peminjan/dasbor B53 ZF A X
