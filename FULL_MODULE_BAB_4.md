
=========================================
PAGE 1
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
1

BAB IV
MODUL PEMINJAMAN

4.1
 
Katalog Alat dan Keranjang Peminjaman

4.1.1)
 
Buat kelas layanan keranjang

Buat folder
 
app/Services/
, lalu buat berkas
 
app/Services/Keranjang.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_1_img_1.png] ---
<?php
namespace App\Services;
use App\Models\Alat;
use Illuminate\Support\Facades\Session;
class Keranjang
{
private const KUNCI_SESSION = ‘keranjang';
public function isiMentah(): array
1
return Session ::get (self ::KUNCI_SESSION, [1);
bi
public function isi()
{
$isiMentah = $this—isiMentan();
if (empty($isiMentah)) {
return collect();
r
$daftarAlat = Alat::with('kategori')
—whereIn('id', array_keys($isiMentah))
—get();
return $daftarAlat—map (function ($alat) use ($isiMentah) {
return (object) [
‘alat' = $alat,
*jumlah' = $isiMentan[$alat—id],
1
1:
t


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_1_img_2.png] ---
public function tambah(Alat $alat, int $jumlan): void
N $isiMentan = $this—isiventan();
$jumiahBaru = ($isiMentah[$alat—id] 22 6) + $jumlah;
$isimentan[$alat—id] = min($jumlanBaru, $alat—stok_tersedia);
Session ::put(self::KUNCI_SESSION, $isiMentan);
+
public function ubahJumlah(Alat $alat, int $jumlan): void
N $isiMentan = $this—isiventan();
if (1 isset($isiMentan[$alat—id])) {
return;
+
$isiMentan[$alat—id] = min($jumian, $alat—stok_tersedia);
Session ::put(self::KUNCI_SESSION, $isiMentan);
+


=========================================
PAGE 2
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
2

Kenapa dibuat kelas tersendiri, bukan ditulis di controller? Keranjang akan dipakai di dua tempat:
controller katalog hari ini, dan controller pengajuan. Kalau logikanya ditulis di controller, kode yang
sama harus disalin dua kali, dan perbaikan di satu tempat akan terlupa di tempat lain. Kelas
layanan seperti ini juga jauh lebih mudah dijelaskan saat sidang, karena input, proses, dan
outputnya jelas.

Perhatikan method
 
tambah()
. Baris
 
($isiMentah[$alat->id] ?? 0) + $jumlah
 
adalah alat hanya
muncul satu kali, jumlahnya digabung. Baris
 
min(..., $alat->stok_tersedia)
 
mencegah keranjang
berisi lebih banyak dari yang tersedia.

Konstanta
 
KUNCI_SESSION
 
dibuat agar nama kunci session hanya ditulis satu kali. Salah ketik nama
kunci adalah kesalahan yang tidak memunculkan pesan apa pun
 
—
 
keranjang hanya terlihat selalu
kosong.

4.1.2)
 
Buat controller katalog

Pada terminal ketikan perintah membuat controller :

php artisan make:controller KatalogController

Buka file
 
app\Http\Controllers\KatalogController.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_2_img_3.png] ---
public function hapus(int $alatId): void
{
$isimentah = $this—isiMentan();
unset ($isiMentan[$atat1a]);
Session ::put(self::KUNCI_SESSION, $isiMentah);
+
public function kosongkan(): void
{
Session ::forget(self ::KUNCI_SESSION) ;
+
public function jumlahBaris(): int
{
return count($this—isiMentah());
+
public function kosong(): bool
{
return $this—jumlahBaris() = 6;
+
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_2_img_4.png] ---
use Illuminate\Http\Request;
use App\Models\Alat;

use App\Models\Kategori;
use App\services\Keranjang;


=========================================
PAGE 3
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
3

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_3_img_5.png] ---
class KatalogController extends Controller
{
public function _construct(private Keranjang $keranjang)
{
+
public function katalog(Request $request)
{
$katakunci = $request—query(‘cari');
$kategorild = $request—query('kategori_id');
$daftarAlat = Alat::with('kategori')
—when($katakunci, function ($query, $katakunci) {
$query—where (function ($cabang) use ($kataKunci) {
$cabang—where('nama', 'like', '%' . $kataKunci . '%')
—orWhere('kode_alat', 'like', '%' . $kataKunci . '%');
Bb;
2)
—when($kategorild, function ($query, $kategoriId) {
$query—where ("kategori_id', $kategorild);
2)
—orderBy(*nama’)
—paginate (18)
—withquerystring();
$daftarkategori = Kategori::orderBy('nama')—get();
return view('katalog.daftar', compact(
‘daftarAlat’, ‘daftarKategori', 'katakunci', 'kategorild'
DH
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_3_img_6.png] ---
public function tambahKeKeranjang(Reguest $reguest, Alat $alat)
{
$data = $request—validate([
‘jumlan' = ['required’, ‘integer’, 'min:1', ‘max: . $alat—>stok_tersedial,
1,
‘jumlah.max' => 'Jumlah melebihi stok yang tersedia (* . $alat—stok_tersedia . ' unit).',
*jumlah.min® = ‘Jumlah minimal 1 unit.®,
n;
if ($alat—sstok_tersedia < 1) {
return back()—with('gagal’, 'Alat ini sedang tidak tersedia.');
+
$this—keranjang—tamban($alat, (int) $datal'jumian']);
return back()—with('sukses', $alat—nama . ' ditambahkan ke keranjang.');
+
public function linatKeranjang()
{
$isiKeranjang = $this—keranjang—isi();
return view('katalog.keranjang', compact('isiKeranjang'));
+
public function ubahJumlah(Request $request, Alat $alat)
{
$data = $request—validate([
‘jumlan' = ['required’, ‘integer’, 'min:1', ‘max: . $alat—>stok_tersedial,
n;
$this—keranjang—ubahJumlanh($alat, (int) $datal'jumian']);
return back()—with('sukses', ‘Jumlah diperbarui.');
+


=========================================
PAGE 4
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
4

Perhatikan
 
__construct(private Keranjang $keranjang)
. Laravel membuatkan objek
 
Keranjang

secara otomatis dan menyerahkannya ke controller. Anda tidak perlu menulis
 
new Keranjang()
 
di
mana pun. Cara ini disebut dependency injection, dan sudah Anda pakai tanpa sadar sejak lewat
parameter
 
Request $request
.

Batas
 
'max:' . $alat->stok_tersedia
 
pada validasi adalah lapis pertama. Kelas Keranjang masih
memberi lapis kedua lewat min(), karena stok bisa saja berkurang di sela-sela peminjam menekan
tombol.

4.1.3)
 
Daftarkan route

Edit file
 
routes\web.php
 
:

Perhatikan route hapus: parameternya bernama
 
{alatId}
 
dan di controller diterima sebagai int

$alatId
, bukan objek Alat. Alasannya, alat yang sudah dihapus admin bisa saja masih tertinggal di
keranjang seseorang. Kalau parameternya berupa objek, Laravel akan mencari datanya, tidak
menemukan, lalu menampilkan HTTP 404
 
—
 
dan peminjam terjebak dengan keranjang yang tidak
bisa dibersihkan.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_4_img_7.png] ---
public function hapusDariKeranjang(int $alatId)
N $this—keranjang—hapus ($alatld);
return back()—with(sukses', ‘Alat dikeluarkan dari keranjang.');
+
public function kosongkanKeranjang()
N $this—keranjang—kosongkan();
return back()—witn(*sukses', ‘Keranjang dikosongkan.');
b +


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_4_img_8.png] ---
/]...
use App\Http\Controllers\KatalogController;
T1158
Route: :middleware([ ‘auth'])->group(function () {
7a
Route: :middleware('permission:alat.lihat")
->prefix('katalog')
->name( 'katalog.")
->group(function () {
Route: :get('/', [KatalogController::class, ‘katalog'])->name(‘'daftar');
Route: :get('/keranjang', [KatalogController::class, 'lihatKeranjang'])->name('keranjang’);
Route: :post('/{alat}/tambah', [KatalogController::class, 'tambahKeKeranjang'])->name('tambah");
Route: :put('/{alat}/jumlah’, [KatalogController::class, 'ubahJumlah’])->name('ubah-jumlah');
Route: :delete('/{alatId}/hapus’, [KatalogController::class, 'hapusDariKeranjang'])->name( hapus');
Route: :delete('/kosongkan', [KatalogController::class, 'kosongkanKeranjang'])->name('kosongkan');
1s
1s


=========================================
PAGE 5
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
5

4.1.4)
 
Buat halaman katalog

Buat
 
resources/views/katalog/daftar.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_5_img_9.png] ---
@extends('layouts.utama*)
@section('judul', ‘Katalog Alat')
@section('konten')
<n4 class="mb-3">Katalog Alat</h4>
@include('katalog. form-pencarian')
<div class="row g-3">
@forelse ($daftarAlat as $alat)
<div class="col-md-6 col-lg-4">
<div class="card h-108">
@if ($alat—foto)
<img src="{{ asset('gambar/alat/' . $alat—foto) }H"
class="card-ing-top" style="height: 168px; object-fit: cover;"
alt="{{ $alat—nama }}">
else
<div class="bg-secondary-subtle d-flex align-items-center justify-content-center”
style="height: 168px;">
<span class="text-muted small®>Tanpa foto</span>
</div>
@endif
<div class="card-body">
<div class="d-flex justify-content-between align-items-start">
<hé class="card-title mb-1">{{ $alat—nama }}</n6>
<span class="badge bg-{{ $alat—stok_tersedia > © ? 'success' : ‘secondary’ }}">
{{ $alat—stok_tersedia }} tersedia
</span>
</div>
<p class="text-muted small mb-2">
{{ $alat—kode_alat }} &niddot; {{ $alat—kategori—nama }}
</p>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_5_img_10.png] ---
@if ($alat—stok_tersedia > 0)
<form method="P0ST" action="{{ route('katalog.tambah', $alat) }*
class="row g-2">
esr
<div class="col-4">
<input type="number* name="jumlah® class="form-control form-control-sm"
value="1" min="1" max="{{ $alat—stok_tersedia }}" required>
</div>
<div class="col-8">
<button type="submit" class="btn btn-sm btn-primary w-108">
Tambah ke Keranjang
</button>
</div>
</form>
else
<button class="btn btn-sm btn-secondary w-180" disabled
Stok Habis
</button>
endif
</div>
</div>
</div>
@enpty
<div class="col-12">
<p class="text-muted">Alat tidak ditemukan.</p>
</div>
@endforelse
</div>
<div class="mt-4">
{{ $daftaratat—iinks() 1+
</div>
@endsection


=========================================
PAGE 6
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
6

Buat
 
resources\views\katalog\form-pencarian.blade.php
:

Alat yang stoknya habis tetap ditampilkan, tetapi tombolnya dimatikan. Menyembunyikannya sama
sekali justru membingungkan
 
—
 
peminjam akan mengira alat itu tidak pernah ada.

4.1.5)
 
Buat halaman keranjang

Buat
 
resources/views/katalog/keranjang.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_6_img_11.png] ---
~ <form method="6ET" action="{{ route('katalog.daftar') }}" class="row g-2 mb-4">
~ <div class="col-md-4">
v <input type="text" name="cari" class="form-control"
placeholder="Cari nama atau kode alat" value="{{ $kataKunci }}">
</div>
~ <div class="col-md-3">
v <select name="kategori_id" class="form-select">
<option value="">Semua Kategori</option>
v @foreach ($daftarKategori as $kategori)
v <option valve="{{ $kategori—id }"
{{ $kategorild = $kategori—id ? ‘selected’ : '' }>
{{ $kategori—snama }}
</option>
@endforeach
</select>
</div>
v <div class="col-auto">
<button type="submit" class="btn btn-outline-secondary”>Cari</button>
<a href="{{ route('katalog.daftar') }}" class="btn btn-outline-secondary">Reset</a>
</div>
</form>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_6_img_12.png] ---
(@extends ('layouts.utama')
@section(*judul', ‘Keranjang Peminjaman')
@section('konten')
<div class="d-flex justify-content-between align-items-center mb-3">
<h4 class="mb-8">Keranjang Peminjaman</né>
<a href="{{ route('katalog.daftar') }}" class="btn btn-outline-secondary">
Kembali ke Katalog
</a>
</div>
@if ($isikeranjang—isEmpty())
<div class="card">
<div class="card-body text-center text-muted py-5">
Keranjang masih kosong. Pilih alat dari Katalog terlebin dahulu.
</div>
</div>


=========================================
PAGE 7
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
7

Buat
 
resources\views\katalog\tabel-keranjang.blade.php
 
:

Tombol "Lanjut ke Pengajuan" masih berisi tanda pagar. Alamat tujuannya akan dibuat nanti.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_7_img_13.png] ---
else
<div class="card">
<div class="card-body">
<div class="table-responsive">
i @include('katalog.tabel-keranjang')
</div>
<div class="d-flex justify-content-between mt-3">
<form method="P0OST" action="{{ route('katalog.kosongkan') }}"
onsubmit="return confirm('Kosongkan seluruh keranjang?*)">
@csrf
@method(* DELETE")
<button type="submit" class="btn btn-outline-danger”>Kosongkan</button>
</form>
<a href="#" class="btn btn-success">
Lanjut ke Pengajuan
</a>
</div>
</div>
</div>
endif
@endsection


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_7_img_14.png] ---
<table class="table align-middle">
<thead>
<tr>
<th>Kode</th>
<th>Nama Alat</th>
<th>Kategori</th>
<th class="text-center">Tersedia</th>
<th style="width: 168px">Jumlah Pinjame/th>
<th style="width: 98px">Aksi</th>
</tr>
</thead>
<tbody>
@foreach ($isiKeranjang as $baris)
<tr>
<td>{{ $baris—alat—kode_alat }}</td>
<td>{{ $baris—alat—nama }}</td>
<td>{{ $baris—alat—kategori—nama }}</td>
<td class="text-center">{{ $baris—alat—stok_tersedia }}</td>
<td>
<form method="POST" action="{{ route('katalog.ubah-jumlah', $baris—alat) }}" class="d-flex gap-1">
@csrf
@method('PUT*)
<input type="number" name="jumlah" class="form-control form-control-sm"
valve="{{ $baris—jumlah }}" min="1" max="{{ $baris—alat—stok_tersedia }}">
<button type="submit" class="btn btn-sm btn-outline-primary">
ubah
</button>
</form>
</td>
<td>
<form method="POST" action="{{ route('katalog.hapus', $baris—alat—id) ">
@csrf
@method (* DELETE")
<button type="submit" class="btn btn-sm btn-danger">
Hapus
</button>
</form>
</td>
</tr>
Qendforeach
</tbody>
</table>


=========================================
PAGE 8
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
8

4.1.6)
 
Tambahkan penanda keranjang di layout

Buka
 
resources/views/layouts/navbar.blade.php
. Ganti tautan Katalog Alat, lalu tambahkan satu
menu keranjang di bawahnya:

4.1.7)
 
Uji

Penanda angka pada menu memberi umpan balik bahwa alat benar-benar masuk keranjang, tanpa
peminjam perlu membuka halamannya.

Masuk sebagai
 
peminjam
, lalu:

No
 
Percobaan
 
Hasil yang harus muncul

1
 
Buka menu Katalog Alat
 
12 alat tampil dalam bentuk kartu, ada penanda
stok tersedia

2
 
Saring kategori Perangkat Jaringan
 
Tersisa 3 kartu

3
 
Tambahkan Multimeter Digital
sebanyak 2 unit

Muncul pesan hijau, penanda keranjang
menunjukkan angka 1

4
 
Tambahkan Multimeter Digital lagi
sebanyak 3 unit

Penanda tetap 1, dan di halaman keranjang
jumlahnya menjadi 5

5
 
Tambahkan Kamera Mirrorless
sebanyak 5 unit

Ditolak, muncul pesan jumlah melebihi stok
tersedia

6
 
Buka halaman Keranjang, ubah
jumlah menjadi 1

Berhasil

7
 
Hapus satu baris dari keranjang
 
Baris hilang, penanda berkurang

8
 
Masuk sebagai admin, buka /katalog
 
HTTP 403

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_8_img_15.png] ---
@can('alat.lihat')
<li class="nav-iten">
<a class="nav-link" href="{{ route('katalog.daftar') }}">
Katalog Alat
</a>
</li>
<li class="nav-item">
<a class="nav-link" href="{{ route('katalog.keranjang') }}">
Keranjang
@if (count(session('keranjang', [1)) > 8)
<span class="badge bg-warning text-dark">
{{ count (session(*keranjang', [1)) }
</span>
endif
</a>
</li>
@endcan


=========================================
PAGE 9
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
9

Halaman Katalog Alat :

Halaman Keranjang :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_9_img_16.png] ---
v  @ Katalog Alt x + = x
« C © localhost8000/katalog Bg (oust) :
Peminjaman Alat Katalog Alat Keranjang Siswa Pe [ Keluar |

Katalog Alat
Cari nama atau kode alat Semua Kategori v | cari || Reset |
Tanpa foto Tapa foto Tanpa foto
Jangka Sorong a= Kamera Mirrorless aD Kunci Pas Set a=
AUK-002 - Alat Ukur AVI-003 - Perangkat Audio Visual PKT-003 - Perkakas Tangan
1 Tambah ke Keranjang 1 Tambah ke Keranjang 1 Tambah ke Keranjang
)
EE |
Tanpa foto Tanpa foto srg,
. Sw
LAN Tester [ 5 tersedia | Mistar Baja 30 cm [15 tersedia Multimeter Digital [ 10 tersedia |
JAR-002 - Perangkat Jaringan AUK-003 - Alat Ukur AUK-001 - Alat Ukur
1 Tambah ke Keranjang 1 Tambah ke Keranjang ; Tambah ke Keranjang
Tanpa foto Tanpa foto Tanpa foto
© Request Timeline Views @ Queries @@) Models @) Gate @ Cache @ © 12x R28ME © 104s GET /katalog B38 = ~ X


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_9_img_17.png] ---
v  @ Keranjang Peminjaman x + - Oo x
« CG © localhost:8000/katalog/keranjang tg (Bouvet) :
Peminjaman Alat Katalog Alat  Keranjang Siswa Peminja [ fam. J
Keranjang Peminjaman [ Kembali ke Katalog |

Kode Nama Alat Kategori Tersedia  Jumlah Pinjam Aksi
AUK-001 Multimeter Digital Alat Ukur 10 1
AVI-003 Kamera Mirrorless Perangkat Audio Visual 2 1
EE
9 Request Timeline Views @) Queries @ Models @ Gate @ Cache @ % 12x 8 28MB © 5s0ms GET /katalog/keranjang 3 = A X


=========================================
PAGE 10
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
10

4.2
 
Menyimpan Pengajuan Peminjaman dalam Satu Transaksi

4.2.1)
 
Buat enum status peminjaman

Buat folder
 
app/Enums/,
 
lalu file
 
app/Enums/StatusPeminjaman.php
:

Method
 
bolehKe()
 
adalah terjemahan langsung dari diagram transisi yang Anda gambar. Buka
kembali berkas itu dan bandingkan baris demi baris
 
—
 
keduanya harus cocok persis.

Setiap kali status hendak diubah, cukup satu baris pemeriksaan. Tanpa enum ini, pemeriksaan yang
sama akan ditulis ulang di beberapa controller dengan kemungkinan salah ketik yang berbeda-
beda.

Method
 
warna()
 
menyimpan nama kelas Bootstrap. Menaruhnya di sini, bukan di Blade, membuat
warna status seragam di seluruh halaman.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_10_img_18.png] ---
<?php
namespace App\Enums;
enum StatusPeminjaman: string
{
case Diajukan = 'diajukan’;
case Ditolak = 'ditolak’;
case Dipinjam = ‘dipinjam‘;
case MenungguVerifikasi = ‘menunggu_verifikasi';
case Selesai = 'selesai’;
public function label(): string
{
return match ($this) {
self: Diajukan = ‘Diajukan’,
self ::Ditolak = ‘Ditolak’,
self ::Dipinjam = ‘Dipinjam’,
self ::MenungguVerifikasi = ‘Menunggu Verifikasi',
self ::Selesai = 'selesai’,
Ig
+
public function warna(): string
{
return match ($this) {
self: Diajukan = ‘warning’,
self ::Ditolak = ‘danger’,
self ::Dipinjam = ‘primary’,
self ::MenungguVerifikasi = ‘info’,
self ::Selesai = ‘success’,
Ig
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_10_img_19.png] ---
public function bolehKe(self $tujuan): bool
{
return match ($this) {
self::Diajukan = in_array ($tujuan, [
self::Dipinjam,
self::Ditolak,
1, true),
self::Dipinjam = $tujuan = self ::MenungguVerifikasi,
self::MenungguVerifikasi = $tujuan = self::Selesai,
self::Ditolak, self::Selesai = false,
Bg
+
+


=========================================
PAGE 11
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
11

4.2.2)
 
Lengkapi relasi model

Buka
 
app/Models/Peminjaman.php
 
dan tambahkan cast serta relasi:

Berkat cast
 
StatusPeminjaman::class
, kolom
 
status
 
yang berupa teks di basis data otomatis berubah
menjadi objek enum saat dibaca. Karena itu Anda bisa langsung menulis
 
$peminjaman->status-
>label()
 
di Blade.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_20.png] ---
use App\Enums\StatusPeminjaman;
class Peminjaman extends Model
{
/ / “ee
protected $casts = [
Hfooc
‘status’ => StatusPeminjaman::class,
1;
public function peminjam()
{
return $this->belongsTo(User::class, ‘user_id');
}
public function petugas()
{
return $this->belongsTo(User::class, 'petugas_id');
}
public function detail()
{
return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id');
}
public function pengembalian()
{
return $this->hasOne(Pengembalian::class, 'peminjaman_id');
}
public function lewatTenggat(): bool
{
return in_array($this->status, [
StatusPeminjaman: :Dipinjam,
StatusPeminjaman: :MenungguVerifikasi,
1, true) && $this->tgl_harus_kembali->isPast();
}
}


=========================================
PAGE 12
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
12

Buka juga
 
app/Models/DetailPeminjaman.php
:

Jangan lupa menambahkan pernyataan
 
use
 
yang diperlukan di setiap berkas.

4.2.3)
 
Buat kelas layanan peminjaman

Buat file
 
app/Services/PeminjamanService.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_12_img_21.png] ---
Ml coc
class DetailPeminjaman extends Model
{
Noo
public function peminjaman()
{
return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
}
public function alat()
{
return $this->belongsTo(Alat::class, 'alat_id');
}
}


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_12_img_22.png] ---
use App\Enums\StatusPeminjaman;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class Peminjamanservice
{
public function _construct(private Keranjang $keranjang)
{
+
public function daftarTunggakan(User $peminjam)
{
return Peminjaman ::where('user_id', $peminjam—id)
—whereIn('status’, [
statusPeminjaman:: Dipinjan—yvalue,
StatusPeminjaman:: Menungguverifikasi—value,
n
—whereDate ('tgl_harus_kembali', '<', now()—toDateString())
—get();
+


=========================================
PAGE 13
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
13

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_13_img_23.png] ---
public function buatPengajuan(User $peminjam, array $data): Peminjaman
{
$isiKeranjang = $this—keranjang—isi();
if ($isiKeranjang—isEmpty()) {
throw ValidationException ::withMessages([
*keranjang' = 'Keranjang masih kosong.®,
n;
+
if ($this—daftarTunggakan($peminjam)—>isNotEmpty()) {
throw ValidationException ::withMessages([
*keranjang' => ‘Anda masih memiliki peminjaman yang lewat tenggat.®,
n;
+
return DB::transaction(function () use ($peminjam, $data, $isiKeranjang) {
$peminjaman = Peminjaman::create([
"kode_pinjam*® = $this—buatKodePinjam($data['tgl_pinjam']),
user_id* = $peminjam—id,
"tgl_pinjam* = $datal'tgl_pinjam'],
*tgl_harus_kembali' => $datal’tgl_harus_kembali'],
‘status’ = StatusPeminjaman:: Diajukan,
"keperluan® = $datal'keperlvan'] 2? null,
n;
foreach ($isiKeranjang as $baris) {
$peminjaman—detail()—create([
‘alat_id' = $baris—alat—id,
*jumlah' = $baris—jumlah,
n;
+
$this—keranjang—kosongkan();
return $peminjaman;
BH;
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_13_img_24.png] ---
private function buatKodePinjam(string $tanggal): string
{
$awalan = 'PJM-' . date('vmd', strtotime($tanggal)) . '-';
$kodeTerakhir = Peminjaman::where('kode_pinjam', 'like', $awalan . '%')
—orderByDesc(*kode_pinjan')
—lockForUpdate()
—value('kode_pinjam');
$nomorUrut = $kodeTerakhir
? ((int) substr($kodeTerakhir, -3)) + 1
= a
return $awalan . str_pad($nomorUrut, 3, '8', STR_PAD_LEFT);
+
public function maksHariPinjam(): int
{
return (int) Pengaturan::ambil('maks_hari_pinjam', 36);
+
public function defaultHariPinjam(): int
{
return (int) Pengaturan::ambil(*default_hari_pinjam', 7);
+
+
os |


=========================================
PAGE 14
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
14

Tiga bagian yang perlu dipahami betul:

DB::transaction()
. Seluruh isi closure dijalankan sebagai satu kesatuan. Bila ada satu saja yang
melempar kesalahan, seluruh perubahan dibatalkan otomatis. Bandingkan dengan

DB::beginTransaction()
 
yang akan Anda pakai keduanya melakukan hal yang sama, hanya bentuk
penulisannya berbeda.

lockForUpdate()
. Method ini menghasilkan
 
SELECT ... FOR UPDATE
, yang mengunci baris hasil
pencarian sampai transaksi selesai. Bila dua peminjam menekan Ajukan pada detik yang sama, yang
kedua akan menunggu sampai yang pertama selesai, sehingga nomor urutnya tidak kembar. Indeks
unik pada kolom
 
kode_pinjam
 
yang Anda pasang tetap menjadi jaring pengaman terakhir:
seandainya penguncian gagal, basis data akan menolak nomor kembar dan transaksi dibatalkan,
bukan menghasilkan data rusak.

$peminjaman->detail()->create([...])
. Karena dipanggil lewat relasi, kolom
 
peminjaman_id
 
diisi
Laravel secara otomatis. Menuliskannya manual bukan salah, hanya lebih panjang dan lebih rawan.

4.2.4)
 
Buat Form Request pengajuan

Pada terminal buat request :

php artisan make:request PengajuanRequest

Buka
 
file app\Http\Requests\PengajuanRequest.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_14_img_25.png] ---
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Pengaturan;
class PengajuanRequest extends FormRequest
{
public function authorize(): bool
{
return $this—user()—>can('peminjaman.ajukan');
+
public function rules(): array
{
$maksHari = (int) Pengaturan::ambil('maks_hari_pinjam', 30);
$batasAkhir = now()—addDays ($maksHari) —>toDatestring();
return [
'tgl_pinjam' = [
‘required’, ‘date’, ‘after_or_equal:today’,
Ig
'tgl_harus_kembali' = [
‘required’, ‘date’,
*after_or_equal:tgl_pinjan',
"before_or_equal:' . $batasAkhir,
Tg
*keperluan' => ['nullable’, ‘string’, 'max:560'],
1
+


=========================================
PAGE 15
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
15

Aturan durasi maksimal ditulis di
 
withValidator()
, bukan di
 
rules()
, karena perhitungannya
melibatkan dua isian sekaligus. Nilai
 
maks_hari_pinjam
 
dibaca dari tabel pengaturan, bukan ditulis
sebagai angka tetap.

4.2.5)
 
Buat controller peminjaman

Pada terminal buat controller :

php artisan make:controller PeminjamanController

Buka file
 
app\Http\Controllers\PeminjamanController.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_15_img_26.png] ---
use App\Http\Requests\PengajuanRequest;
use App\Models\Peminjaman;
use App\services\Keranjang;
use App\services\Peminjamanservice;
use Illuminate\Validation\ValidationException;
class PeminjamanController extends Controller
{
public function _construct(
private PeminjamanService $layanan,
private Keranjang $keranjang
IH
+
public function formPengajuan()
{
if ($this—keranjang—kosong()) {
return redirect()
—route('katalog.daftar')
—with(‘gagal’, 'Pilin alat terlebih dahulu sebelum mengajukan.');
+
$isiKeranjang = $this—keranjang—isi();
$daftarTunggakan = $this—layanan—daftarTunggakan(auth()—user());
$defaultHari = $this—layanan—defaultHariPinjam();
$maksHari = $this—layanan—maksHariPinjam();
return view('peminjaman.form', compact (
‘isiKeranjang', ‘daftarTunggakan', 'defaultHari’, ‘maksHari®
D;
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_15_img_27.png] ---
public function simpanPengajuan(PengajuanRequest $request)
{
try {
$peminjaman = $this—1layanan—buatPengajuan(
auth ()-suser(),
$request—validated()
di
} catch (validationException $e) {
return back()—withErrors($e—verrors())—withInput();
+
return redirect ()
—route(*peninjanan.saya*)
—with('sukses', 'Pengajuan ' . $peminjaman—kode_pinjam . ' berhasil dikirim.');
+


=========================================
PAGE 16
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
16

Baris
 
abort_unless($peminjaman->user_id === auth()->id(), 403)
 
mencegah seorang peminjam
melihat pengajuan milik peminjam lain hanya dengan mengganti angka pada alamat. Kelalaian
semacam ini adalah lubang keamanan yang sangat umum, dan penguji biasanya mengujinya.

4.2.6)
 
Daftarkan route

Edit file
 
routes\web.php
 
:

Route
 
{peminjaman}
 
sengaja diletakkan paling bawah. Kalau ditaruh di atas, alamat
 
/peminjaman/saya

akan tertangkap olehnya, dan Laravel mencari peminjaman dengan id bernama "saya".

Buka
 
resources/views/katalog/keranjang.blade.php
, ganti tombol "Lanjut ke Pengajuan" menjadi:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_16_img_28.png] ---
public function daftarsaya()
{
$daftarPeminjaman = Peminjaman::with(['detail.alat'])
—where('user_id*, auth()-id())
—orderByDesc('created_at')
—paginate (18);
return view('peminjaman.saya*, compact(*daftarPeminjaman'));
+
public function rincian(Peminjaman $peminjaman)
{
abort_unless($peminjaman—user_id == auth()—id(), 483);
$peminjaman—1oad(['detail.alat’, 'petugas']);
return view(*peminjaman.rincian*, compact(*peminjaman*));
+
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_16_img_29.png] ---
fs oe
use App\Http\Controllers\PeminjamanController;
fs oe
Route: :middleware([ "auth'])->group(function () {
{fs
Route: :middleware( 'permission:peminjaman.ajukan")
->prefix('peminjaman’)
->name( ‘peminjaman.")
->group (function () {
Route: :get('/ajukan', [PeminjamanController::class, 'formPengajuan'])->name('ajukan');
Route: :post('/ajukan', [PeminjamanController::class, 'simpanPengajuan'])->name('simpan’);
Route: :get('/saya’, [PeminjamanController::class, ‘daftarSaya'])->name('saya’);
Route: :get('/{peminjaman}', [PeminjamanController::class, 'rincian'])->name('rincian’);
Hs
1s


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_16_img_30.png] ---
<a href="{{ route('peminjaman.ajukan') }}" class="btn btn-success">
Lanjut ke Pengajuan
</a>


=========================================
PAGE 17
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
17

Tambahkan pula menu baru di Navbar, di dalam blok
 
@can('alat.lihat')
:

4.2.7)
 
Buat halaman form pengajuan

Buat
 
resources/views/peminjaman/form.blade.php
:

Buat file
 
resources\views\peminjaman\alat-diajukan.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_17_img_31.png] ---
@can( 'alat.lihat')
Kle-iiei==>
<li class="nav-item">
<a class="nav-1link" href="{{ route('peminjaman.saya') }}">Pinjaman Saya</a>
</1i>
@endcan


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_17_img_32.png] ---
(extends('layouts.utama’)
@section(*judul’, *Ajukan Peminjaman')
 @section('konten')
<h4 class="mb-3">Ajukan Peminjaman</né>
~ @if ($daftarTunggakan—isNotEmpty())
v <div class="alert alert-danger”>
<strong>Pengajuan tidak dapat dikirim.</strong>
Anda masin memiliki peminjaman yang lewat tenggat:
v <ul class="mb-8 mt-2">
~ @foreach ($daftarTunggakan as $tunggakan)
v <u>
{{ $tunggakan—kode_pinjam } — jatuh tempo
{{ $tunggakan—tgl_harus_kembali—>format('d/m/Y') }}
</li>
@endforeach
</ul>
</div>
endif
v <div class="row">
“ <div class="col-md-7 mp-3*>
@include(’peminjaman.alat-diajukan')
</div>
v <div class="col-md-5">
@include(’peminjaman.data-peminjam')
</div>
</div>
@endsection


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_17_img_33.png] ---
v <div class="card">
<div class="card-header">Alat yang Diajukan</div>
v <div class="card-body p-8">
“ <table class="table mp-0">
“ <thead>
“ <tr>
<th>Kode</th>
<th>Nama Alat</th>
<th class="text-center">Jumlah</th>
</tr>
</thead>
“ <tbody>
v @foreach ($isiKeranjang as $baris)
“ <tr>
<td>{{ $baris—alat—kode_alat }}</td>
<td>{{ $baris—alat—nama }}</td>
<td class="text-center>{{ $baris—jumian } </td>
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

Buat file
 
resources\views\peminjaman\data-peminjam.blade.php
 
:

Tombol kirim dimatikan bila ada tunggakan, tetapi pemeriksaan sebenarnya tetap ada di

PeminjamanService
. Tampilan hanya memberi tahu lebih awal; yang menegakkan aturan tetap kode
di belakangnya.

4.2.8)
 
Buat halaman daftar dan rincian

Buat
 
resources/views/peminjaman/saya.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_18_img_34.png] ---
~ <div class="card">
<div class="card-header">Data Peminjaman</div>
v <div class="card-body">
~ @error(‘keranjang')
<div class="alert alert-danger">{{ $message }}</div>
Qenderror
v <form method="POST" action="{{ route('peminjaman.simpan') }}">
@csrf
<x-input label="Tanggal Pinjam" type="date" name="tgl_pinjam"
:value="old('tgl_pinjam', now()—toDateString())* required />
<x-input label="Tanggal Harus Kembali® type="date" name="tgl_harus_kembali"
:value="old('tgl_harus_kembali', now()—addDays($defaultHari)—>toDateString())" required />
v <div class="form-text">
Durasi bawaan {{ $defaultHari }} hari, maksimal {{ $maksHari }} hari.
</div>
<x-textarea label="Keperluan® name="keperluan® :value="old('keperluan')" />
~ <button type="submit" class="btn btn-primary w-100" {{ $daftarTunggakan—isNotEmpty() ? ‘disabled’ : '' }>
Kirin Pengajuan
</button>
N <a href="{{ route('katalog.keranjang') }}* class="btn btn-secondary w-160 mt-2">
Kembali ke Keranjang
</a>
</form>
</div>
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_18_img_35.png] ---
(@extends ('layouts.utama')
@section(*judul', *Pinjaman Saya')
@section('konten')
<n4 class="mb-3">Pinjaman Saya</hé>
<div class="card">
<div class="card-body">
<div class="table-responsive">
@include(*peminjaman. tabel-pinjam')
</div>
{{ $daftarPeminjaman—links() }}
</div>
</div>
@endsection


=========================================
PAGE 19
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
19

Buat file
 
resources\views\peminjaman\tabel-pinjam.blade.php
 
:

Buat
 
resources/views/peminjaman/rincian.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_19_img_36.png] ---
<table class="table table-striped align-middie">
“ <thead>
“ <tr>
<th>Kode Pinjam</tn>
<th>Tanggal Pinjam</th>
<th>Harus Kembali</th>
<th class="text-center">Jumlah Alat</th>
<th>Status</th>
<th style="width: 188px">Aksi</th>
</tr>
</thead>
“ <tbody>
v @forelse ($daftarPeminjaman as $peminjaman)
“ <tr>
<td>{{ $peminjaman—kode_pinjam }}</td>
<td>{{ $peminjaman—tgl_pinjam—format(*d/m/v') }}</td>
“ <td>
{{ $peminjaman—tgl_harus_kembali—format('d/m/Y') }
w @if ($peminjaman—1lewatTenggat())
<span class="badge bg-danger”>Lewat tenggat</span>
endif
</td>
<td class="text-center">{{ $peminjaman—detail—count() }}</td>
“ <td>
v <span class="badge bg-{{ $peminjaman—status—warna() }">
{{ $peminjaman—status—1label() }+
</span>.
</td>
“ <td>
“ <a href="{{ route("peminjaman.rincian', $peminjaman) }}"
class="btn btn-sm btn-outline-primary*>Rincian</a>
</td>
</tr>
“ @enpty
“ <tr>
“ <td colspan="6" class="text-center text-muted">
Belum ada pengajuan peminjaman.
</td>
</tr>
@endforelse
</tbody>
</table>
eh


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_19_img_37.png] ---
@extends('layouts.utama*)
@section(*judul', ‘Rincian Peminjaman')
@section('konten')
<div class="d-flex justify-content-between align-items-center mb-3">
<h4 class="mb-8">{{ $peminjaman—kode_pinjam }}</né4>
<a href="{{ route('peminjaman.saya') }}" class="btn btn-outline-secondary">Kembali</a>
</div>
<div class="card mp-3">
<div class="card-body">
<dl class="row mb-8">
<dt class="col-sm-3">Status</dt>
<dd class="col-sm-9">
<span class="badge bg-{{ $peminjaman—status—warna() ">
{{ $peminjaman—status—1label() }}
</span>.
</dd>


=========================================
PAGE 20
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
20

4.2.9)
 
Uji

Masuk sebagai peminjam:

No
 
Percobaan
 
Hasil yang harus muncul

1
 
Isi keranjang dengan 2 jenis alat, lalu
Lanjut ke Pengajuan

Form tampil, tanggal kembali sudah terisi 7
hari ke depan

2
 
Kirim pengajuan
 
Berhasil, muncul kode PJM- diikuti tanggal
dan -001

3
 
Periksa tabel peminjaman dan
detail_peminjaman di phpMyAdmin

Satu baris induk, dua baris detail, status
diajukan

4
 
Periksa kolom stok_tersedia pada tabel alat
 
Belum berubah
 
—
 
inilah BR-01

5
 
Buat pengajuan kedua di hari yang sama
 
Kode berakhiran -002

6
 
Ajukan dengan tanggal kembali 45 hari ke
depan

Ditolak, muncul pesan durasi maksimal 30
hari

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_20_img_38.png] ---
<dt class="col-sm-3">Tanggal Pinjam</dt>
<dd class="col-sn-9*>{{ $peminjaman—tgl_pinjan—format(*d/m/Y*) }}</dd>
<dt class="col-sm-3">Harus Kembali</dt>
<dd class="col-sn-9*>{{ $peminjaman—ytgl_harus_kembali—>format('d/m/y') }}</dd>
<dt class="col-sm-3">Keperluan</dt>
<dd class="col-sm-9">{{ $peminjaman—keperlvan 2: '-' } </dd>
<dt class="col-sm-3">Petugas</dt>
<dd class="col-sm-9">{{ $peminjaman—petugas—nama ?? 'Belum diproses' }}</dd>
@if ($peminjaman—alasan_tolak)
<dt class="col-sm-3">Alasan Ditolak</dt>
<dd class="col-sm-9 text-danger”>{{ $peminjaman—alasan_tolak }}</dd>
endif
<ai>
</div>
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_20_img_39.png] ---
<div class="card">
<div class="card-header">Daftar Alat</div>
<div class="card-body p-8">
<table class="table mp-0">
<thead>
<tr>
<th>Kode</th>
<th>Nama Alat</th>
<th class="text-center">Jumlah</th>
</tr>
</thead>
<tbody>
@foreach ($peminjaman—detail as $baris)
<tr>
<td>{{ $baris—alat—kode_alat }}</td>
<td>{{ $baris—alat—nama }}</td>
<td class="text-center">{{ $baris—jumlah } </td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
@endsection


=========================================
PAGE 21
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
21

No
 
Percobaan
 
Hasil yang harus muncul

7
 
Ajukan dengan tanggal kembali sebelum
tanggal pinjam

Ditolak

8
 
Buka Pinjaman Saya
 
Dua pengajuan tampil dengan lencana kuning
"Diajukan"

9
 
Buka rincian pengajuan milik orang lain
dengan mengganti angka di alamat

HTTP 403

Untuk menguji BR-11, ubah satu baris lewat phpMyAdmin: setel status menjadi dipinjam dan
tgl_harus_kembali menjadi tanggal minggu lalu. Lalu coba ajukan peminjaman baru
 
—
 
harus ditolak
dengan daftar tunggakan yang tampil.

Percobaan nomor 4 sering mengejutkan siswa. Stok memang belum boleh berkurang saat
pengajuan masuk, karena pengajuan bisa saja ditolak. Pengurangan stok baru terjadi saat petugas
menyetujui, dan itu pekerjaan JS-13.

Halaman Ajukan Peminjaman :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_21_img_40.png] ---
v  @ Ajuken Peminjaman x + - oOo x
« CGC  @ localhost:8000/peminjaman/ajukan tg (Bouvet) :
Peminjaman Alat Katalog Alat  Keranjang Pinjaman Saya Siswa Peminja [ Ketuar |

Ajukan Peminjaman
Alat yang Diajukan Data Peminjaman
Kode Nama Alat Jumlah Tanggal Pinjam
AVI-001 Proyektor Portabel 1 08/15/2026 a
Tanggal Harus Kembali
08/22/2026 [=]
Durasi bawaan 7 hari, maksimal 30 hari.
Keperluan
Pembelajaran
4
Kirim Pengajuan
Kembali ke Keranjang
29 Request Timeline Views @ Queries @) Models @ Gate @ Cache @ © 12x B28ME © 539ms GET /peminjaman/ajukan B53 = A X


=========================================
PAGE 22
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
22

Halaman Pinjam Saya :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_22_img_41.png] ---
v © Pinaman Saye x + - oOo x
« CG © localhost:8000/peminjaman/saya Bg (ous) :
Peminjaman Alat Katalog Alat  Keranjang Pinjaman Saya Siswa Peminja [ Keluar |

Pinjaman Saya
Kode Pinjam Tanggal Pinjam Harus Kembali Jumlah Alat Status Aksi
PIM-20260815-003 15/08/2026 15/08/2026 1 cs
PIM-20260815-002 15/08/2026 22/08/2026 1 [ Dicjuken |
PIM-20260815-001 15/08/2026 22/08/2026 2 cs
©9 Request Timeline Views @ Queries @) Models @P Gate @ Cache @ % 12x B28M8 © 454ms GET /peminjaman/saya B35 = A X


=========================================
PAGE 23
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
23

4.3
 
Stored Procedure Persetujuan dan Antrian Petugas

4.3.1)
 
Buat migration stored procedure

Pada terminal buat migration :

php artisan make:migration create_sp_setujui_peminjaman

Buka file
 
database\migrations\..._create_sp_setujui_peminjaman.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_23_img_42.png] ---
© use Illuminate\Database\Migrations\Migration;

“use Illuminate\support\Facades\DB;

© return new class extends Migration

7 1

8 public function up(): void

9 1

16 DB ::unprepared(*DROP PROCEDURE IF EXISTS sp_setujui_peminjaman');
1

12 DB ::unprepared(*

13 CREATE PROCEDURE sp_setujui_peminjaman(

14 IN p_peminjaman_id BIGINT UNSIGNED,

15 IN p_petugas_id  BIGINT UNSIGNED

16 )

17 BEGIN

18 DECLARE v_selesai INT DEFAULT ©;

19 DECLARE v_alat_id  BIGINT UNSIGNED;

20 DECLARE v_jumlah INT;

2 DECLARE v_tersedia INT;

22 DECLARE v_nama_alat VARCHAR(150);

23 DECLARE v_status  VARCHAR(38);

2 DECLARE v_pesan  VARCHAR(255);

25 DECLARE v_kode VARCHAR(20) ;

27 DECLARE kursor_detail CURSOR FOR

28 SELECT alat_id, jumlan

29 FROM detail _peminjaman

36 WHERE peminjaman_id = p_peminjaman_id;

32 DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_selesai = 1;
34 — Pemeriksaan 1: status harus masih diajukan.

35 SELECT status, kode_pinjam INTO v_status, v_kode

36 FROM peminjaman WHERE id = p_peminjaman_id;

38 IF v_status IS NULL THEN

El SIGNAL SQLSTATE '45000"

40 SET MESSAGE_TEXT = ‘Data peminjaman tidak ditemukan';
a END IF;

42

43 IF v_status < ‘diajukan' THEN

44 SIGNAL SQLSTATE '45000"

45 SET MESSAGE_TEXT = 'Peminjaman ini sudah pernah diproses’;
46 END IF;


=========================================
PAGE 24
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
24

Beberapa hal yang wajib dipahami:

Hal
 
Penjelasan

Tidak ada DELIMITER
 
Perintah DELIMITER adalah fitur klien phpMyAdmin, bukan
perintah SQL. Menuliskannya di dalam DB::unprepared justru
menyebabkan kesalahan sintaks. Nanti pada JS-21, saat
menyiapkan berkas .sql untuk dikumpulkan, DELIMITER harus
ditambahkan

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_24_img_43.png] ---
a
48 — Pemeriksaan 2: seluruh baris alat harus mencukupi stoknya.
49 OPEN kursor_detail;

50

51 periksa_stok: LOOP

52 FETCH kursor_detail INTO v_alat_id, v_jumlan;
53

54 IF v_selesai = 1 THEN

55 LEAVE periksa_stok;

56 END IF;

57

58 SELECT stok_tersedia, nama

59 INTO v_tersedia, v_nama_alat

60 FROM alat WHERE id = v_alat_id FOR UPDATE;
61

62 IF v_tersedia < v_jumlah THEN

63 SET v_pesan = CONCAT(

64 *Stok tidak mencukupi untuk ', v_nama_alat,
65 * (diminta *, v_jumlah, ', tersedia ', v_tersedia, ')*
66 DH

67

68 CLOSE kursor_detail;

69

70 SIGNAL SQLSTATE *45000"

7 SET MESSAGE_TEXT = v_pesan;

72 END IF;

73 END LOOP;

74

75 CLOSE kursor_detail;


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_24_img_44.png] ---
77 — Semua baris lolos: kurangi stok sekaligus.

78 UPDATE alat a

79 JOIN detail_peminjaman d ON d.alat_id = a.id

80 SET a.stok_tersedia = a.stok_tersedia - d.jumlah
81 WHERE d.peminjaman_id = p_peminjaman_id;

82

83 UPDATE peminjaman

84 SET status = 'dipinjam’,

ES petugas_id = p_petugas_id,

86 updated_at = NOW()

87 WHERE id = p_peminjaman_id;

88

89 INSERT INTO log_aktivitas

90 (user_id, aksi, tabel_tujuan, deskripsi, created at)
91 VALUES

92 (p_petugas_id, ‘setujui’, ‘peminjaman’,

93 CONCAT('Menyetujui peminjaman *, v_kode), NOW());
94 END

95 =z

9% ¥

97

98 public function down(): void

99 1

160 DB:: unprepared (‘DROP PROCEDURE IF EXISTS sp_setujui_peminjaman');
101 +

102};

a.


=========================================
PAGE 25
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
25

SIGNAL SQLSTATE '45000'
 
Cara resmi melempar kesalahan buatan sendiri dari dalam
MariaDB. Kesalahan ini akan diterima Laravel sebagai
QueryException

FOR UPDATE di dalam kursor
 
Mengunci baris alat sampai transaksi selesai, sehingga stok
tidak bisa dibaca dan dikurangi dua permintaan sekaligus

CLOSE kursor_detail sebelum
SIGNAL

Kursor yang ditinggalkan terbuka akan menahan sumber daya.
Selalu tutup sebelum keluar dari prosedur

Pemeriksaan dipisah dari
pengurangan

Seluruh baris diperiksa lebih dulu, baru stok dikurangi. Dengan
cara ini, pengajuan berisi 3 alat yang satu di antaranya kurang
stok tidak akan mengurangi stok dua alat lainnya

Jalankan migration:

php artisan migrate

Periksa di phpMyAdmin: pilih basis data, buka tab Routines. Prosedur
 
sp_setujui_peminjaman
 
harus
terdaftar di sana.

4.3.2)
 
Uji prosedur langsung di phpMyAdmin

Sebelum menyambungkannya ke Laravel, buktikan dulu prosedurnya benar. Ini menghemat waktu:
bila nanti ada masalah, Anda sudah tahu letaknya bukan di SQL.

Catat dulu stok awal:

SELECT id, nama, stok, stok_tersedia FROM alat;

SELECT id, kode_pinjam, status FROM peminjaman WHERE status = 'diajukan';

Panggil prosedurnya, ganti angka sesuai data Anda:

CALL sp_setujui_peminjaman(1, 2);

Angka pertama adalah id peminjaman, angka kedua adalah id petugas. Setelah itu periksa ulang:

SELECT id, nama, stok, stok_tersedia FROM alat;

SELECT id, kode_pinjam, status, petugas_id FROM peminjaman WHERE id = 1;

SELECT * FROM log_aktivitas ORDER BY id DESC LIMIT 3;

Yang harus terjadi:
 
stok_tersedia
 
berkurang sesuai jumlah yang
 
dipinjam
, status berubah menjadi
dipinjam,
 
petugas_id
 
terisi, dan ada satu baris log baru.

Sekarang panggil sekali lagi prosedur yang sama:

CALL sp_setujui_peminjaman(1, 2);

Harus muncul pesan kesalahan "Peminjaman ini sudah pernah diproses". Inilah bukti pemeriksaan
status berjalan.

=========================================
PAGE 26
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
26

Kembalikan keadaan seperti semula sebelum lanjut:

php artisan migrate:fresh --seed

php artisan permission:cache-reset

Lalu buat ulang satu-dua pengajuan lewat antarmuka peminjam.

4.3.3)
 
Tambahkan method persetujuan ke layanan

Buka
 
app/Services/PeminjamanService.php
, tambahkan pernyataan use berikut di bagian atas:

use App\Models\LogAktivitas;

Lalu tambahkan tiga method ini di dalam kelas:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_26_img_45.png] ---
public function setujui(
Peninjaman $peminjaman,
int $petugasId,
?string $tenggatBaru = null
): void {
abort_untess(
$peminjaman—status—bolenKe (StatusPeminjaman :: Dipinjam),
422,
‘Status peminjaman tidak memungkinkan untuk disetujui.’
):
DB ::beginTransaction();
try {
if ($tenggatBaru && $tenggatBaru == $peminjaman—>tgl_harus_kembali—>toDateString()) {
$tenggatlama = $peminjaman—tgl_harus_kembali—toDateString();
$peminjaman—update (['tgl_harus_kembali' = $tenggatBarul);
LogAktivitas:: create ([
‘user_id" = $petugasId,
‘aksi’ = ‘ubah_tenggat’,
*tabel_tujuan' = ‘peminjaman',
‘deskripsi' = ‘Tenggat ' . $peminjaman—skode_pinjam
. * diubah dari ' . $tenggatLama
. ' menjadi ' . $tenggatBaru,
*ip_address’ = request()—ip(),
103
3
DB::statement(*CALL sp_setujui_peminjaman(?, 2)*, [
$peminjaman—id,
$petugasId,
n;
DB: commit();
} catch (\Throwable $e) {
DB::rollBack();
throw $e;
+
+


=========================================
PAGE 27
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
27

Perhatikan letak
 
abort_unless
. Pemeriksaan transisi status memakai method
 
bolehKe()
 
dari enum
yang Anda buat. Prosedur di basis data juga memeriksa hal yang sama. Dua lapis ini tidak mubazir:
yang di aplikasi memberi pesan yang bisa dibaca, yang di basis data menjaga data tetap benar apa
pun yang terjadi.

Perhatikan pola
 
try
–
catch
. Bila stok tidak mencukupi, prosedur melempar
 
SIGNAL
, Laravel
mengubahnya menjadi
 
QueryException
, blok catch menjalankan
 
rollBack()
, lalu melempar ulang
kesalahannya agar controller bisa menampilkan pesan. Perubahan tenggat yang mungkin sudah
tersimpan sebelum pemanggilan prosedur ikut dibatalkan
 
—
 
inilah gunanya membungkus
keduanya dalam satu transaksi.

Method
 
tolak()
 
memakai
 
DB::transaction()
, sementara
 
setujui()
 
memakai
 
beginTransaction
.
Keduanya setara. Bentuk panjang dipakai pada
 
setujui()
 
supaya Anda dapat menunjukkan
 
commit

dan
 
rollBack
 
secara eksplisit saat sidang, sesuai butir penilaian pada soal.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_27_img_46.png] ---
public function tolak(Peminjaman $peminjaman, int $petugasId, string $alasan): void
{
abort_untess(
$peminjaman—sstatus—bolenke(StatusPeminjanan :: Ditolak),
422,
‘Status peminjaman tidak memungkinkan untuk ditolak.'
):
DB ::transaction(function () use ($peminjaman, $petugasid, $alasan) {
$peminjaman—update ([
‘status’ = StatusPeminjaman::Ditolak,
'petugas_id* = $petugasId,
"alasan_tolak' = $alasan,
n;
LogAktivitas::create([
user_id* = $petugasId,
‘aksit = ‘tolak’,
*tabel_tujuan' = ‘peminjaman’,
‘deskripsi' = 'Menolak peminjaman ' . $peminjaman-—kode_pinjam,
'ip_address’ = request()—ip(),
n;
BH;
+
public function antrianPengajuan()
{
return Peninjaman::with([*peminjan*, ‘detail.alat'])
—where('status', StatusPeminjaman::Diajukan—value)
—orderBy(*created_at*)
—paginate (18);
}


=========================================
PAGE 28
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
28

4.3.4)
 
Buat controller persetujuan

Pada terminal buat controller :

php artisan make:controller PersetujuanController

Buka file
 
app\Http\Controllers\PersetujuanController.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_28_img_47.png] ---
use App\Models\Peminjaman;
use App\services\Peminjamanservice;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
class PersetujuanController extends Controller
vi
public function _construct(private Peminjamanservice $layanan)
{
+
public function antrian()
~ {
$daftarPengajuan = $this—layanan—antrianPengajuan();
return view('persetujuan.antrian', compact('daftarPengajuan’));
+
public function rincian(Peminjaman $peminjaman)
~ {
$peminjaman—1oad(['peminjam’, 'detail.alat']);
return view('persetujuan.rincian', compact(’peminjaman®));
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_28_img_48.png] ---
public function setujui(Request $request, Peminjaman $peminjaman)
{
$data = $request—validate([
'tgl_harus_kembali' = [
‘required’, ‘date’,
‘after_or_equal:' . $peminjaman—tgl_pinjam—toDateString(),
jis
n;
try {
$this—layanan—setujuil
$peninjaman,
auth()—id(),
$datal’ tgl_harus_kembali']
di
} catch (QueryException $e) {
return back()—witn('gagal’, $this—pesanRaman($e));
+
return redirect()
—route('persetujuan.antrian’)
—with('sukses', 'Peminjaman ' . $peminjaman—kode_pinjam . ' disetujui.');
+


=========================================
PAGE 29
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
29

Method
 
pesanRamah()
 
mengambil teks yang Anda tulis di
 
SET MESSAGE_TEXT
 
pada stored procedure.
Dengan begitu, pesan "Stok tidak mencukupi untuk Proyektor Portabel (diminta 5, tersedia 3)"
yang disusun di dalam basis data sampai utuh ke layar petugas.

4.3.5)
 
Daftarkan route dan menu

Edit file
 
routes\web.php
 
:

Ganti tautan menu Persetujuan di Navbar menjadi
 
{{ route('persetujuan.antrian') }}.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_29_img_49.png] ---
public function tolak(Request $request, Peminjaman $peminjaman)
{
$data = $request—validate([
‘alasan_tolak' => [‘required’, ‘string’, 'min:5', 'max:560'],
1,
‘alasan_tolak.required' = 'Alasan penolakan wajib diisi.’,
*alasan_tolak.min" = 'Alasan penolakan minimal 5 Karakter.',
n;
$this—layanan—tolak($peminjaman, auth()—id(), $datal'alasan_tolak'l);
return redirect()
—route('persetujuan.antrian’)
—with('sukses', 'Peminjaman ' . $peminjaman—kode_pinjam . ' ditolak.');
+
private function pesanRamah(QueryException $e): string
{
$pesanAsli = $e—errorInfol2] 22 '';
return $pesanAsli == '*
? $pesanAsli
© 'Persetujuan gagal diproses. Silakan coba lagi.';
+
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_29_img_50.png] ---
{Es
use App\Http\Controllers\PersetujuanController;
ffir
Route: :middleware([ "auth'])->group(function () {
[lees
Route: :middleware( 'permission:peminjaman.setujui‘)
->prefix('persetujuan’)
->name( 'persetujuan.’)
->group (function () {
Route::get('/', [PersetujuanController::class, ‘antrian'])->name(‘antrian’);
Route: :get('/{peminjaman}', [PersetujuanController::class, ‘rincian’'])->name('rincian');
Route: :post('/{peminjaman}/setujui’, [PersetujuanController::class, ‘setujui’'])->name('setujui‘);
Route: :post(’/{peminjaman}/tolak"’, [PersetujuanController::class, ‘'tolak'])->name('tolak');
IH
1s


=========================================
PAGE 30
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
30

4.3.6)
 
Buat halaman antrian dan rincian

Buat
 
resources/views/persetujuan/antrian.blade.php
:

Buat
 
resources\views\persetujuan\tabel-antrian.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_30_img_51.png] ---
@extends('layouts.utama*)
@section(*judul', *Antrian Pengajuan')
@section('konten')
<n4 class="mb-3">Antrian Pengajuan</hé>
<div class="card">
<div class="card-body">
<div class="table-responsive">
@include(*persetujuan. tabel-antrian*)
</div>
{{ $daftarPengajuan—links() }
</div>
</div>
@endsection


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_30_img_52.png] ---
v| <table class="table table-striped align-middle">
“ <thead>
“ <tr>
<th>Kode Pinjame/th>
<th>Peminjam</th>
<th>Tanggal Pinjame/th>
<th>Harus Kembali</th>
<th class="text-center">Jumlah Alat</th>
<th style="width: 108px">Aksi</th>
</tr>
</thead>
“ <tbody>
v @forelse ($daftarPengajuan as $peminjaman)
“ <tr>
<td>{{ $peminjaman—kode_pinjam }}</td>
<td>{{ $peminjanan—peminjan—nama }} </td>
<td>{{ $peminjaman—tgl_pinjam—format('d/m/Y*) } </td>
<td>{{ $peminjaman—tgl_harus_kembali—>format('d/m/¥') }}</td>
<td class="text-center">{{ $peminjaman—detail—count() }}</td>
“ <td>
~ <a href="{{ route('persetujuan.rincian', $peminjaman) }}" class="btn btn-sm btn-primary">
Proses
</a>
</td>
</tr>
“ empty
“ <tr>
“ <td colspan="6" class="text-center text-muted">
Tidak ada pengajuan yang menunggu diproses.
</td>
</tr>
@Gendforelse
</tbody>
</table>


=========================================
PAGE 31
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
31

Buat
 
resources/views/persetujuan/rincian.blade.php
:

Buat
 
resources\views\persetujuan\alat-diajukan.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_31_img_53.png] ---
(@extends ('layouts.utama')
2 @section('judul', ‘Proses Pengajuan')
@section('konten')
<div class="d-flex justify-content-between align-items-center mb-3">
<h4 class="mb-8">{{ $peminjaman—kode_pinjam }}</né4>
<a href="{{ route('persetujuan.antrian’) }}" class="btn btn-outline-secondary">Kembali</a>
</div>
<div class="row">
<div class="col-md-7 mb-3">
@include(*persetujuan.alat-diajukan*)
</div>
<div class="col-md-5">
@include(*persetujuan. form-setujui')
</div>
</div>
@endsection


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_31_img_54.png] ---
<div class="card">
<div class="card-header">Alat yang Diajukan</div>
<div class="card-body p-8">
<table class="table mp-0">
<thead>
<tr>
<th>Kode</th>
<th>Nama Alat</th>
<th class="text-center">Diminta</th>
<th class="text-center">Tersedia</th>
</tr>
</thead>
<tbody>
@foreach ($peminjaman—detail as $baris)
<tr class="{{ $baris—alat—>stok_tersedia < $baris—jumlah ? 'table-danger' : '' }}">
<td>{{ $baris—alat—kode_alat }}</td>
<td>{{ $baris—alat—nama }}</td>
<td class="text-center>{{ $baris—jumian }</td>
<td class="text-center">{{ $baris—alat—stok_tersedia }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
<div class="card mt-3">
<div class="card-body">
<dl class="row mb-8">
<dt class="col-sm-4">Peminjam</dt>
<dd class="col-sm-8">{{ $peminjaman—peminjam—nama }}</dd>
<dt class="col-sm-4">Tanggal Pinjam</dt>
<dd class="col-sn-8">{{ $peminjaman—>tgl_pinjan—format(*d/m/Y*) }}</dd>
<dt class="col-sm-4">Keperluan</dt>
<dd class="col-sm-8">{{ $peminjaman—keperilvan 2: '-' } </dd>
</d>
</div>
</div>


=========================================
PAGE 32
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
32

Buat
 
resources\views\persetujuan\form-setujui.blade.php
 
:

Baris alat yang stoknya kurang diberi latar merah lewat kelas table-danger. Petugas jadi tahu
sebelum menekan tombol bahwa persetujuan kemungkinan akan gagal.

4.3.7)
 
Uji, termasuk pembuktian rollback

Masuk sebagai
 
petugas
:

No
 
Percobaan
 
Hasil yang harus muncul

1
 
Buka menu Persetujuan
 
Pengajuan berstatus diajukan tampil di antrian

2
 
Catat stok_tersedia alat terkait di
phpMyAdmin

Simpan tangkapan layarnya sebagai keadaan sebelum

3
 
Setujui satu pengajuan
 
Berhasil, hilang dari antrian

4
 
Periksa lagi stok_tersedia
 
Berkurang sesuai jumlah
 
—
 
tangkapan layar keadaan
sesudah

5
 
Periksa tabel peminjaman
 
Status dipinjam, petugas_id terisi

6
 
Periksa tabel log_aktivitas
 
Ada baris beraksi setujui yang ditulis oleh stored
procedure

7
 
Setujui pengajuan sambil
mengubah tanggal kembali

Berhasil, dan ada baris log beraksi ubah_tenggat

8
 
Tolak satu pengajuan tanpa
mengisi alasan

Ditolak validasi

9
 
Tolak dengan alasan terisi
 
Status menjadi ditolak, dan stok_tersedia tidak
berubah

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_32_img_55.png] ---
<div class="card mb-3">
<div class="card-header">Setujui</div>
<div class="card-body">
<form method="P0ST" action="{{ route('persetujuan.setujui’', $peminjaman) }}">
eset
<x-input label="Tanggal Harus Kembali” name="tgl_harus_kembali® type="date"
:value="$peminjaman—tgl_harus_kembali—>toDateString()" required />
<button type="submit" class="btn btn-success w-108">
Setujui Peminjaman
</button>
</form>
</div>
</div>
<div class="card">
<div class="card-header">Tolak</div>
<div class="card-body">
<form method="P0ST" action="{{ route('persetujuan.tolak', $peminjaman) }}">
eset
<x-textarea label="Alasan Penolakan" name="alasan_tolak" rows="3" required />
<button type="submit" class="btn btn-danger w-108">
Tolak Peminjaman
</button>
</form>
</div>
</div>


=========================================
PAGE 33
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
33

Halaman Persetujuan :

Halaman Rincian Persetujuan :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_33_img_56.png] ---
Vv @ antrian Pengajusn x + - oOo x
« GC  @ localhost:8000/persetujuan Bg (ows) i
Peminjaman Alat  Persetujuan Petugas Laboratoriu [ Keluar |
Antrian Pengajuan
Kode Pinjam Peminjam Tanggal Pinjam Harus Kembali Jumlah Alat ~~ Aksi |
PIM-20260816-003 Siswa Peminjam 16/08/2026 23/08/2026 2
@9 Request Timeline Views @ Queries @ Models @ Gate @ Cache @ % 12x B® 28Me © 760ms GET /persetujuan B35 = A X


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_33_img_57.png] ---
Vv @ Proses Pengajuan x + - oOo x
« C © localhost:8000/persetujuan/3 Bg (ows) i
Peminjaman Alat  Persetujuan Petugas Laborato [ Keluar |
PJM-20260816-003 | Kembali |

Alat yang Diajukan Setujui |
Kode Nama Alat Diminta Tersedia Tanggal Harus Kembali
AVI-001 Proyektor Portabel 1 3 08/23/2026 a
AVI-003 Kamera Mirrorless 1 2

Peminjam Siswa Peminjam

Tanggal Pinjam 16/08/2026 Tolak

Keperluan Seminar Alasan Penolakan

4
Tolak Peminjaman
29 Request Timeline Views @ Queries @) Models @ Gate @ Cache @ © 12x B29ME © S67ms GET /persetujuan/3 BB = A X
