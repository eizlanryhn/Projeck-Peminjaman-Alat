
=========================================
PAGE 1
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
1

BAB III
MASTER DATA

3.1
 
CRUD Kategori sebagai Pola Dasar

3.1.1)
 
Tambahkan relasi pada model Kategori

Buka
 
app/Models/Kategori.php
 
dan tambahkan satu method relasi:

Relasi ini akan dipakai untuk menghitung berapa alat yang bernaung di tiap kategori.

3.1.2)
 
Buat Form Request

Pada terminal ketikan perintah membuat Request :

php artisan make:request KategoriRequest

Isi file
 
app/Http/Requests/KategoriRequest.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_1_img_1.png] ---
[lees
class Kategori extends Model
{
Ileos
public function daftarAlat()
{
return $this->hasMany(Alat::class, ‘kategori_id');
}
}


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_1_img_2.png] ---
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class KategoriRequest extends FormRequest
{
public function authorize(): bool
{
return $this—user()—can('kategori.kelola');
+
public function rules(): array
{
$kategorivangDiubah = $this—route('kategori');
return [
‘nama’ = [
‘required’,
‘string’,
*max:180* ,
Rule ::unique('kategori®, *nama‘)
—>ignore ($kategorivangDiubah),
Ig
*deskripsi' = ['nullable’, ‘string’, 'max:1600'],
1
+
public function messages(): array
{
return [
‘nama.required’ => ‘Nama kategori wajib diisi.’,
‘nama.unique’ => ‘Nama Kategori tersebut sudah terdaftar.',
*nama.max* = ‘Nama Kategori maksimal 160 Karakter.',
1
+
}


=========================================
PAGE 2
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
2

Tiga hal yang membuat berkas ini penting:

Bagian
 
Fungsi

authorize()
 
Lapis kedua pemeriksaan izin, setelah middleware pada route. Bila izin dicabut,
permintaan ditolak walau route-nya terlanjur terbuka

rules()
 
Pemenuhan NFR-05: validasi dilakukan di sisi server, bukan hanya di peramban

->ignore()
 
Saat mengubah data, nama kategori itu sendiri tidak boleh dianggap duplikat
dirinya sendiri

Variabel
 
$kategoriYangDiubah
 
bernilai kosong saat menyimpan data baru, dan berisi objek kategori
saat mengubah. Satu berkas Form Request karena itu cukup untuk melayani dua keperluan.

3.1.3)
 
Buat controller

Pada terminal buat controller :

php artisan make:controller KategoriController --model=Kategori

Buka file
 
app\Http\Controllers\KategoriController.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_2_img_3.png] ---
use App\Http\Reguests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
class KategoriController extends Controller
{
public function index(Request $request)
{
$katakunci = $request—query(‘cari');
$daftarkategori = Kategori::withCount('daftarAlat’)
—swhen($kataKunci, function ($query, $katakunci) {
$query—where('nama’, 'like', '%' . $kataKunci . '%');
2)
—orderBy(*nama’)
—paginate (18)
—withquerystring();
return view('kategori.index', compact('daftarKategori’, ‘kataKunci'));
+
public function create()
{
$kategori = new Kategori();
return view('kategori.form', compact('kategori'));
+
public function store(KategoriRequest $request)
{
Kategori ::create($request—validated());
return redirect()
—route('kategori.index')
—with('sukses', ‘Kategori berhasil ditambahkan.');
+


=========================================
PAGE 3
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
3

Tentang blok
 
try
–
catch
. Foreign key
 
alat.kategori_id
 
memakai
 
restrictOnDelete()
, jadi MariaDB
menolak penghapusan kategori yang masih dipakai. Tanpa penangkapan
 
QueryException
, pengguna
melihat halaman error berwarna merah yang tidak dipahami. Dengan penangkapan ini, ia melihat
kalimat yang menjelaskan masalahnya. Inilah cara utuh
 
—
 
aturannya ditegakkan basis data,
pesannya disampaikan aplikasi.

Tentang
 
withQueryString()
. Tanpa method ini, kata kunci pencarian hilang saat pengguna
berpindah ke halaman kedua.

3.1.4)
 
Daftarkan route

Tambahkan ke dalam grup
 
auth
 
di
 
routes/web.php
:

Seluruh grup dilindungi
 
permission:kategori.kelola
. Karena izin itu hanya dimiliki peran admin
pada seeder, petugas dan peminjam otomatis menerima HTTP 403 bila mencoba membuka alamat
ini.

Nama parameter
 
{kategori}
 
harus sama persis dengan nama variabel
 
Kategori $kategori
 
di
controller. Kesamaan nama inilah yang membuat Laravel otomatis mencarikan datanya dari basis
data.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_3_img_4.png] ---
public function edit(Kategori $kategori)
{
return view('kategori.form', compact('kategori'));
+
public function update(KategoriRequest $request, Kategori $kategori)
{
$kategori—update ($request—validated());
return redirect()
—route('kategori.index')
—with('sukses', ‘Kategori berhasil diperbarui.');
+
public function destroy(Kategori $kategori)
{
try {
$kategori—delete();
} catch (QueryException $e) {
return redirect()
—route('kategori.index')
—with(‘gagal’, ‘Kategori tidak dapat dihapus karena masih dipakai olen data alat.');
+
return redirect()
—route('kategori.index')
—with('sukses', ‘Kategori berhasil dihapus.');
+
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_3_img_5.png] ---
ooo
use App\Http\Controllers\KategoriController;
Route: :middleware([ "auth'])->group(function () {
Jl coc
Route: :resource( 'kategori®,KategoricController::class)
->except([ "show'])
->middleware( 'permission:kategori.kelola’);
Hs


=========================================
PAGE 4
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
4

Periksa hasilnya di terminal:

php artisan route:list --name=kategori

3.1.5)
 
Buat halaman daftar

Buat file
 
resources/views/kategori/index.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_4_img_6.png] ---
(@extends (*layouts.utama')
@section(*judul', ‘Daftar Kategori')
@section('konten')
<div class="d-flex justify-content-between align-items-center mb-3">
<né class="mb-8">Daftar Kategori</hé>
<x-tombol-tambah :href="route('kategori.create')" label="Tambah Kategori” />
</div>
<div class="card">
<div class="card-body">
<x-form-pencarian :action="route('kategori.index')" :kataKunci="$kataKunci" />
<div class="table-responsive">
<table class="table table-striped align-middle">
<thead>
<tr>
<th style="width: 68px">No</th>
<th>Nama</th>
<th>Deskripsi</th>
<th style="width: 118px">Jumlah Alat</th>
<th style="width: 168px">Aksi</th>
</tr>
</thead>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_4_img_7.png] ---
<tbody>
@forelse ($daftarKategori as $nomor = $kategori)
<tr>
<td>{{ $daftarKategori—firstItem() + $nomor }}</td>
<td>{{ $kategori—nama }}</td>
<td>{{ $kategori—deskripsi ?: '-' }}</td>
<td>{{ $kategori—daftar_alat_count }}</td>
<td>
<x-tombol-aksi
:ubah="route('kategori.edit', $kategori)”
:hapus="route('kategori.destroy*, $kategori)”
pesanHapus="Yakin ingin menghapus kategori {{ $kategori—nama }}2"
5
</td>
</tr>
Qenpty
<tr>
<td colspan="5" class="text-center text-muted">
Belun ada data kategori.
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
4 $daftarkategori—links() I
</div>
</div>
@endsection


=========================================
PAGE 5
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
5

Buat file component
 
resources\views\components\tombol-tambah.blade.php
 
:

Buat file component
 
resources\views\components\form-pencarian.blade.php
 
:

Buat file component
 
resources\views\components\tombol-aksi.blade.php
 
:

Beberapa hal yang perlu dipahami:

•
 
$daftarKategori->firstItem() + $nomor
 
membuat penomoran berlanjut di halaman kedua.
Kalau hanya memakai
 
$nomor + 1
, halaman kedua akan mulai dari angka 1 lagi.

•
 
withCount('daftarAlat')
 
di controller menghasilkan properti bernama
 
daftar_alat_count
.
Namanya diubah otomatis oleh Laravel dari huruf kapital menjadi garis bawah.

•
 
Tombol hapus dibungkus form dengan
 
@method('DELETE')
, karena tautan biasa hanya bisa
mengirim permintaan GET.

•
 
{{ $daftarKategori->links() }}
 
menghasilkan navigasi halaman.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_5_img_8.png] ---
@props([*href', “label' = 'Tambah'l)
<a href="{{ $href }}" class="btn btn-primary">{{ $label }}</a>
3


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_5_img_9.png] ---
@props([*action', 'kataKunci' = '', ‘nama’ = 'cari', ‘placeholder’ = 'Cari...'])
<form method="6ET" action="{{ $action }}" class="row g-2 mb-3">
<div class="col-md-4">
<input type="text" name="{{ $nama }}" class="form-control" placeholder="{{ $placeholder }}"
valve="{{ $katakunci }H">
</div>
<div class="col-auto">
<button type="submit" class="btn btn-outline-secondary”>Cari</button>
<a href="{{ $action }}" class="btn btn-outline-secondary">Reset</a>
</div>
</form>
PE


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_5_img_10.png] ---
@props([
“lihat' = null,
*ubah' = nuit,
*hapus’ = null,
*pesanHapus' => ‘Yakin ingin menghapus data ini?',
n
<div class="d-flex gap-1">
@if ($lihat)
<a href="{{ $lihat }}" class="btn btn-sm btn-info">Linhat</a>
@endif
@if ($ubah)
<a href="{{ $ubah }}" class="btn btn-sm btn-warning">Ubah</a>
@endif
@if ($hapus)
<form action="{{ $hapus }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $pesanHapus }}')">
@csrf
@method (* DELETE")
<button type="submit* class="btn btn-sm btn-danger*>Hapus</button>
</form>
@endif
</div>


=========================================
PAGE 6
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
6

Agar tampilan navigasi halaman memakai gaya Bootstrap, tambahkan baris berikut pada method

boot()
 
di
 
app/Providers/AppServiceProvider.php
:

3.1.6)
 
Buat halaman form

Buat file
 
resources/views/kategori/form.blade.php.
 
Satu berkas dipakai untuk menambah maupun
mengubah data.

Buat file component
 
resources\views\components\textarea.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_6_img_11.png] ---
TURE
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
1

Life wi

public function boot(): void

{

Paginator: :useBootstrapFive();

}

bj


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_6_img_12.png] ---
@extends(*layouts.utama*)
@section('judul', $kategori—exists ? 'Ubah Kategori' : 'Tambah Kategori’)
@section('konten')
<div class="row justify-content-center">
<div class="col-md-7">
<div class="card">
<div class="card-body">
<n5 class="card-title mb-4">
{{ $kategori—exists ? 'Ubah Kategori’ : 'Tambah Kategori }}
</n5>
<form method="POST"
action="{{ $kategori—exists ? route('kategori.update', $kategori) : route('kategori.store') }}">
@esrf
@if ($kategori—sexists)
@method ("PUT")
@endif
<x-input name="nama® label="Nama Kategori" :value="$kategori—nama" />
<x-textarea name="deskripsi” label="Deskripsi Kategori” :value="$kategori->deskripsi® />
<button type="submit” class="btn btn-primary">Simpan</button>
<a href="{{ route('kategori.index') }}" class="btn btn-secondary">Batal</a>
</form>
</div>
</div>
</div>
</div>
@endsection
zz |


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_6_img_13.png] ---
@props([*name*, ‘label’, ‘type’ => ‘text’, ‘value’ = "*])
<div class="mb-3">
<label for="{{ $name }}" class="form-label">{{ $label } </label>
~ <textarea class="form-control @error($name) is-invalid @enderror®
id="{{ $name }}*
name="{{ $name }}"
rows="3"
{{ $attributes 1+
>{{ old($name, $value) }}</textarea>
~  @error($name)
“ <div class="invalid-feedback">
{{ $message 1+
</div>
@enderror
</div>


=========================================
PAGE 7
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
7

Kunci dari form gabungan ini adalah
 
$kategori->exists
. Objek kategori kosong yang dibuat di
method
 
create()
 
bernilai false, sedangkan objek hasil pencarian bernilai true. Dari satu properti itu
ditentukan judul halaman, alamat tujuan form, dan perlu tidaknya
 
@method('PUT')
.

3.1.7)
 
Aktifkan menu dan uji

Buka
 
resources/views/layouts/navbar.blade.php
, ganti tautan menu Kategori menambahkan

route(
‘
kategori.index
’
)
 
pada atribut
 
href
:

No
 
Percobaan
 
Hasil yang harus muncul

1
 
Buka menu Kategori
 
Tampil 4 kategori hasil seeder dengan jumlah alat
masing-masing 3

2
 
Tambah kategori baru
bernama "Alat Kebersihan"

Tersimpan, muncul pesan hijau

3
 
Tambah lagi dengan nama
yang sama

Ditolak, muncul pesan "Nama kategori tersebut sudah
terdaftar"

4
 
Ubah nama kategori tanpa
mengubah apa pun lalu
simpan

Berhasil, tidak dianggap duplikat

5
 
Hapus kategori "Alat
Kebersihan"

Berhasil, muncul pesan hijau

6
 
Hapus kategori "Alat Ukur"
 
Ditolak, muncul pesan merah bahwa kategori masih
dipakai

7
 
Masuk sebagai petugas, buka
alamat /kategori

HTTP 403

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_7_img_14.png] ---
<l== oa ==>
<div class="collapse navbar-collapse" id="menuUtama">
<ul class="navbar-nav me-auto">
@can('kategori.kelola')
<li class="nav-item">
<a class="nav-link" href="{{ route('kategori.index"') }}">Kategori</a>
</1i>
@endcan
<l-= Lo. ==>
</ul>
les coi ==>
</div>


=========================================
PAGE 8
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
8

Halaman Daftar Kategori :

Halaman Form Kategori :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_8_img_15.png] ---
v  @ Daftar Kategori x + - Oo x
« CG @ localhost:8000/kategori Bg (ous) :
Peminjaman Alat Kategori Alat Pengguna Administrato [ Keluar |

Daftar Kategori Tambah Kategori
car. (or [feet]
No Nama Deskripsi Jumlah Alat  Aksi
1 met  ——— =... |
FO VR Tr —— ; |
EE ——— ; =... |
© eres Tongan Obens, tang nc, pl ; |
@9 Request Timeline Views @) Queries @ Models @ Gate @ Cache @ @ 12x B 28M © ol6ms GET /kategori (3 5 A X


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_8_img_16.png] ---
Vv @ Tembah Kategori x + - oOo x
« CG © localhost:8000/kategori/create tg (Bouvet) :
Peminjaman Alat Kategori Alat Pengguna Administrato [ Keluar |

Tambah Kategori
Nama Kategori
Deskripsi Kategori
4
29 Request Timeline Views @ Queries @ Models @ Gate @ Cache @ % 12x B® 28ME © 4%ms GET /kategori/create [3 = A X


=========================================
PAGE 9
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
9

3.2
 
CRUD Alat dengan Relasi dan Unggah Foto

3.2.1)
 
Siapkan penyimpanan File

Edit file
 
config\filesystems.php
 
:

Folder
 
public/gambar
 
harus ada dan bisa ditulis (writable)
 
—
 
kalau belum ada, Laravel biasanya
otomatis membuatnya saat
 
storeAs()
 
dipanggil, tapi lebih aman disiapkan manual dulu.

visibility => 'public'
 
di sini cuma metadata, bukan symlink
 
—
 
karena root disk sudah langsung
di
 
public/
, file otomatis bisa diakses lewat URL tanpa perantara.

Tambahkan
 
public/gambar/*
 
ke
 
.gitignore
 
kalau tidak mau file upload ikut ter-commit (opsional,
tergantung kebutuhan).

3.2.2)
 
Tambahkan relasi pada model Alat

Buka
 
app/Models/Alat.php
:

Relasi
 
kategori()
 
memakai
 
belongsTo
 
karena tabel
 
alat
 
yang menyimpan kolom
 
kategori_id
.
Aturan sederhananya: tabel yang memegang foreign key selalu memakai
 
belongsTo
.

3.2.3)
 
Buat Form Request

Pada terminal buat Request :

php artisan make:request AlatRequest

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_9_img_17.png] ---
‘disks’ => [
Vi BEE
‘gambar’ => [
‘driver’ => 'local’,
‘root’ => public_path('gambar'),
‘url® => env('APP_URL') . '/gambar’,
‘visibility' => ‘public’,
"throw' => false,
1,
1,


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_9_img_18.png] ---
class Alat extends Model
public function kategori()
{
return $this->belongsTo(Kategori::class, 'kategori_id');
ly
public function detailPeminjaman()
{
return $this->hasMany(DetailPeminjaman::class, 'alat_id');
}
}


=========================================
PAGE 10
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
10

Buka file
 
app\Http\Requests\AlatRequest.php
 
:

Aturan
 
'lte:stok'
 
berarti less than or equal terhadap isian bernama
 
stok
 
pada form yang sama.
Aturan ini adalah lapis pertama penjaga invarian
 
0 <= stok_tersedia <= stok
. Lapis keduanya
adalah CHECK constraint yang Anda pasang.

Mengapa perlu dua lapis?
 
Validasi di aplikasi memberi pesan yang bisa dibaca pengguna. CHECK
constraint menjaga data tetap benar walaupun nanti ada kode lain
 
—
 
stored procedure, trigger,
atau query manual
 
—
 
yang menyentuh kolom itu.

3.2.4)
 
Buat controller

Pada terminal buat Controller :

php artisan make:controller AlatController --model=Alat

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_10_img_19.png] ---
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class AlatRequest extends FormRequest
vi
public function authorize(): bool
~ {
return $this—user()—can('alat.kelola');
+
public function rules(): array
~ {
$alatyangDiubah = $this—route('alat');
v return [
*kategori_id' => ['required’, 'exists:Kategori,id'],
v ‘kode_alat' = [
‘required’,
‘string’,
‘max:30°,
Rule::unique(‘alat’, 'kode_alat')—>ignore($alatvangbiubah),
in
*nama’ = ['required’, ‘string’, 'max:156'],
*deskripsi’ = ['nullable’, ‘string’, 'max:1060'],
*stok’ = ['required’, ‘integer’, 'min:0'],
‘stok_tersedia’ => ['required’, ‘integer’, 'min:@', 'lte:stok'l,
*kondisi' = ['required’, Rule::in(['baik, ‘rusak_ringan‘, ‘rusak_berat'l)],
‘foto’ = ['nullable’, ‘image’, ‘mimes:jpg,jpeg,png’, 'max:2048'],
1
+
public function messages(): array
~ {
v return [
‘kategori_id.required’ = ‘Kategori wajib dipilin.*,
*kode_alat. unique" = ‘Kode alat tersebut sudah terdaftar.®,
*stok_tersedia.lte’ = ‘Stok tersedia tidak boleh melebihi stok total.®,
*foto.max* = ‘Ukuran foto maksimal 2 MB.',
*foto.mimes* = ‘Foto harus berformat JPG atau PNG.',
1
+
+


=========================================
PAGE 11
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
11

Buka file
 
app\Http\Controllers\AlatController.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_20.png] ---
use App\Http\Reguests\AlatRequest;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
1
[- AlatController extends Controller
vi
public function index(Request $request)
~ {
$katakunci = $request—query(‘cari');
$kategorild = $request—query('kategori_id');
~ $daftarAlat = Alat::with('kategori')
v —when($kataKunci, function ($query, $katakunci) {
~ $query—where (function ($cabang) use ($kataKunci) {
~ $cabang—where('nama', 'like', '%' . $kataKunci . '%')
—oriihere('kode_alat', 'like', '%' . $kataKunci . '%');
Bb;
2)
w —when($kategorild, function ($query, $kategoriId) {
$query—where ("kategori_id', $kategorild);
2)
—orderBy('kode_alat")
—paginate (18)
—withquerystring();
$daftarkategori = Kategori::orderBy('nama')—get();
~ return view('alat.index', compact(
‘daftarAlat’,
*daftarKategori',
*katakunci',
*kategorild*
D;
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_11_img_21.png] ---
public function create()
{
$alat = new Alat();
$daftarkategori = Kategori::orderBy('nama')—get();
return view('alat.form', compact(‘alat’, 'daftarKategori'));
+
public function store(AlatRequest $request)
{
$data = $request—validated();
if ($request—hasFile('foto')) {
$namaFile = unigid() . '.' . $reguest—file('foto')—extension();
$request—file('foto')—3storeAs( 'alat’, $namaFile, 'gambar');
$datal*foto'] = $namaFile;
+
Alat::create($data) ;
return redirect()
—route(*alat. index’)
—with('sukses', ‘Data alat berhasil ditambahkan.');
+
public function edit(Alat $alat)
{
$daftarkategori = Kategori::orderBy('nama')—get();
return view('alat.form', compact(‘alat’, 'daftarKategori'));
+


=========================================
PAGE 12
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
12

Empat hal yang membedakan controller ini:

Bagian
 
Alasan

Alat::with('kategori')
 
Tanpa
 
with(),
 
menampilkan 10 alat
menghasilkan 11 query: satu untuk alat, sepuluh
untuk kategorinya masing-masing

Blok
 
if ($request->hasFile('foto'))
 
Foto bersifat opsional. Saat mengubah data tanpa
memilih foto baru, foto lama harus tetap dipakai

hapusFoto()
 
sebelum menyimpan yang baru
 
Mencegah berkas menumpuk di server tanpa
pernah dipakai

Urutan pada method
 
destroy()
 
Nama berkas disimpan sebelum baris data
dihapus. Setelah baris hilang,
 
$alat->foto
 
tidak
dapat dibaca lagi

Perhatikan juga bahwa
 
hapusFoto()
 
diberi tanda private. Method itu alat bantu internal, bukan
tujuan sebuah alamat, jadi tidak boleh bisa dipanggil dari route.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_12_img_22.png] ---
public function update(AlatRequest $request, Alat $alat)
{
$data = $request—validated();
if ($request—hasFile('foto')) {
$this—hapusFoto($alat—foto);
$namaFile = unigid() . '.' . $request—file('foto')—extension();
$request—file('foto')—3storeAs( 'alat’, $namaFile, 'gambar');
$datal*foto'] = $namaFile;
+
$alat—update ($data) ;
return redirect()
—route(*alat. index’)
—with('sukses', ‘Data alat berhasil diperbarui.');
+
public function destroy(Alat $alat)
{
try {
$fotoLama = $alat—foto;
$alat—delete();
$this—hapusFoto($fotoLama) ;
} catch (QueryException $e) {
return redirect()
—route('alat. index")
—with(‘gagal’, 'Alat tidak dapat dihapus Karena sudah pernah dipinjam.');
+
return redirect()
—route(*alat. index’)
—with('sukses', ‘Data alat berhasil dihapus.');
+
private function hapusFoto(?string $lokasiFoto): void
{
if ($lokasiFoto 8& Storage ::disk('gambar')—exists('alat/' . $lokasiFoto)) {
Storage :: disk(*gambar')—delete('alat/' . $lokasiFoto);
+
+
+


=========================================
PAGE 13
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
13

Buktikan sendiri manfaat
 
with()
 
nanti: buka halaman daftar alat, lalu lihat jumlah query pada bilah
Debugbar. Coba hapus
 
with('kategori')
, muat ulang, dan bandingkan angkanya.

3.2.5)
 
Daftarkan route

Edit file
 
routes\web.php
 
:

3.2.6)
 
Buat halaman daftar

Buat
 
resources/views/alat/index.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_13_img_23.png] ---
VS
use App\Http\Controllers\KategoriController;
Route: :middleware(['auth'])->group(function () {
List
Route: :resource('alat', AlatController::class)
->except(['show'])
->middleware( 'permission:alat.kelola’);
1s


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_13_img_24.png] ---
(@extends (*layouts.utama')
@section('judul', ‘Daftar Alat')
~ @section('konten')
v <div class="d-flex justify-content-between align-items-center mb-3">
<h4 class="mb-8">Daftar Alat</n&>
<x-tombol-tambah :href="route('alat.create')" label="Tambah Alat" />
</div>
v <div class="card">
“ <div class="card-body">
@include(*alat.form-pencarian')
“ <div class="table-responsive">
“ <table class="table table-striped align-middle">
“ <thead>
“ <tr>
<th style="width: 78px">Foto</th>
<th>Kode</th>
<th>Nama</th>
<th>Kategori</th>
<th class="text-center">Stok</th>
<th class="text-center">Tersedia</th>
<th>Kondisi</th>
<th style="width: 168px">Aksi</th>
</tr>
</thead>


=========================================
PAGE 14
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
14

Buat file
 
resources\views\alat\form-pencarian.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_14_img_25.png] ---
<tbody>
@forelse ($daftarAlat as $alat)
<tr>
<td>
@if ($alat—foto)
<img src="{{ asset('gambar/alat/' . $alat—foto) }H" alt="{{ $alat—nama }}"
class="rounded"” width="4g" height="48" style="object-fit: cover;">
else
<span class="text-muted small">-</span>
@endif
</td>
<td>{{ $alat—kode_alat }}</td>
<td>{{ $alat—snama } </td>
<td>{{ $alat—kategori—nama }}</td>
<td class="text-center">{{ $alat—stok }}</td>
<td class="text-center">
<span class="badge bg-{{ $alat—stok_tersedia > 8 ? 'success' : 'secondary' }}">
{{ $alat—stok_tersedia 1
</span>.
</td>
<td>{{ str_replace(’_*, * *, $alat—kondisi) }} </td>
<td>
<x-tombol-aksi
:ubah="route('alat.edit’, $alat)"
:hapus="route(‘alat.destroy’, $alat)"
pesanHapus="Hapus data alat {{ $alat—nama }}2" />
</td>
</tr>
Gempty
<tr>
<td colspan="8" class="text-center text-muted">
Data alat tidak ditemukan.
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
{1 $daftarAlat—links()
</div>
</div>
@endsection


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_14_img_26.png] ---
<form method="GET" action="{{ route('alat.index') }}" class="row g-2 mb-3">
<div class="col-md-4">
<input type="text" name="cari® class="form-control” placeholder="Cari nama atau kode alat"
value="{{ $kataKunci }">
</div>
<div class="col-md-3">
<select name="kategori_id" class="form-select">
<option value="">Semua Kategori</option>
@foreach ($daftarKategori as $kategori)
<option value="{{ $kategori—id }}" {{ $kategorild = $kategori—id > ‘selected’ : '' }}>
{{ $kategori—snama }}
</option>
@endforeach
</select>
</div>
<div class="col-auto">
<button type="submit" class="btn btn-outline-secondary">Saring</button>
<a href="{{ route('alat.index') }}" class="btn btn-outline-secondary">Reset</a>
</div>
</form>
BO


=========================================
PAGE 15
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
15

3.2.7)
 
Buat halaman form

Buat
 
resources/views/alat/form.blade.php
:

Atribut
 
enctype="multipart/form-data"
 
pada tag form wajib ada. Tanpa itu, berkas foto tidak ikut
terkirim, dan
 
$request->hasFile('foto')
 
selalu bernilai salah tanpa pesan kesalahan apa pun. Ini
kesalahan yang sangat sulit dilacak kalau tidak diketahui sejak awal.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_15_img_27.png] ---
(@extends (*layouts.utama')
@section(*judul', $alat—exists ? 'Ubah Alat' : 'Tambah Alat')
@section('konten')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
<div class="card-body">
<n5 class="card-title mb-4">
{{ $alat—exists ? 'Ubah Data Alat' : 'Tambah Data Alat' }-
</hs>
<form method="POST" action="{{ $alat—exists ? route('alat.update', $alat) : route('alat.store') }}"
enctype="nultipart/forn-data">
@csrf
@if ($alat—exists)
@method ("PUT")
@endif
<div class="row">
<div class="col-md-4">
<x-input name="kode_alat" label="Kode Alat" :value="$alat—kode_alat" />
</div>
<div class="col-md-8">
<x-input name="nama® label="Nama Alat" :value="$alat—nama" />
</div>
</div>
<x-select nama="kategori_id" label="Kategori” :opsi="$daftarKategori” :value="$alat—kategori_id" keyvalue="id"
keyLabel="nama" placenolder="Pilin kategori® />


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_15_img_28.png] ---
<div class="row">
<div class="col-md-4">
<x-input name="stok" label="Stok Total" :value="$alat—3stok" type="number"
min="e" />
</div>
<div class="col-md-4">
<x-input name="stok_tersedia” label="Stok Tersedia” :value="$alat—stok_tersedia” type="number"
min="e" />
</div>
<div class="col-md-4">
<x-select nama="kondisi® label="Kondisi" :opsi="[
['key' = 'baik', 'label' = 'Baik'l,
['key' = ‘rusak_ringan', ‘label’ => ‘Rusak Ringan'],
['key' = ‘rusak_berat', ‘label’ => ‘'Rusak Berat'],
J" :value="$alat—kondisi"
placeholder="Pilih kondisi" />
</div>
</div>
<x-textarea name="deskripsi" label="Deskripsi" rows="3" :value="$alat—deskripsi" />
@include(*alat.input-foto*)
<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('alat.index') }}" class="btn btn-secondary">Batal</a>
</form>
</div>
</div>
</div>
</div>
@endsection


=========================================
PAGE 16
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
16

Buat file
 
resources\views\alat\input-foto.blade.php
 
:

Buat file component
 
resources\views\components\select.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_16_img_29.png] ---
v <div class="mb-3">
<label for="foto" class="forn-label">Foto</label>
v <input type="file" class="form-control @error('foto') is-invalid @enderror® id="foto" name="foto"
accept="inage/jpeg, inage/png">
v @error(* foto)
<div class="invalid-feedback">{{ $message }}</div>
@enderror
v @if ($alat—foto)
“ <div class="mt-2">
<img src="{{ asset('gambar/alat/' . $alat—foto) }" class="rounded" width="186">
“ <div class="form-text">
Biarkan kosong bila tidak ingin mengganti foto.
</div>
</div>
@endif
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_16_img_30.png] ---
~ @props ([
“name,
*label' = null,
topsit = [1,
‘value’ = null,
‘keyValue' = ‘key’,
*keylabel' = ‘label’,
‘placeholder’ = null,
n
<div class="mb-3">
v @if ($lapel)
<label for="{{ $name }}" class="form-label">{{ $label }}</label>
(ST
~ <select class="form-select @error($name) is-invalid @enderror® id="{{ $name }}" name="{{ $name }}">
© @if ($placenolder)
<option valuve="">{{ $placenolder }}</option>
endif
v @foreach ($opsi as $pilihan)
v @php
$nilai = $pilihan($keyvaluel;
$teks = $pilihan[$keyLabell;
@endphp
v <option value="{{ $nilai }H"
{{ (string) old($name, $value) = (string) $nilai ? ‘selected’ : '* H>
{{ $teks +
</option>
(endforeach
</select>
~  @error ($name)
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>


=========================================
PAGE 17
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
17

3.2.8)
 
Aktifkan menu dan uji

Ganti tautan menu Alat di
 
resources\views\layouts\navbar.blade.php
 
menjadi
 
{{
route('alat.index') }}
.

Uji sebagai admin:

No
 
Percobaan
 
Hasil yang harus muncul

1
 
Buka menu Alat
 
12 alat hasil seeder, terbagi dua halaman

2
 
Saring berdasarkan kategori Alat
Ukur

Tersisa 3 baris

3
 
Cari dengan kata kunci "AUK"
 
Tersisa 3 baris, dicocokkan dari kolom kode

4
 
Tambah alat baru lengkap dengan
foto

Tersimpan, foto tampil di daftar

5
 
Tambah alat dengan kode PKT-001
 
Ditolak, muncul pesan kode sudah terdaftar

6
 
Tambah alat dengan stok 5 dan stok
tersedia 9

Ditolak, muncul pesan stok tersedia melebihi stok
total

7
 
Ubah alat tanpa memilih foto baru
 
Foto lama tetap tampil

8
 
Ubah alat dengan memilih foto baru
 
Foto berganti, berkas lama hilang dari
storage/app/public/alat

9
 
Hapus alat yang baru dibuat
 
Berhasil

Halaman Daftar Alat :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_17_img_31.png] ---
v © Daftar lat x + ~ no x
« CG @ localhost8000/alat BL (Down) :
Peminjaman Alat Kategori Alat Pengguna Administrato [ Keluar |

Daftar Alat Tambah Alat
Cari nama atau kode alat Semua Kategori v | saring || Reset |
Foto Kode Nama Kategori Stok Tersedia Kondisi Aksi
we
BRIE ~Ux-001 Multimeter Digital Alt Ukur 12 @ bak [Usa]
=
- AUK-002  Jangka Sorong Alat Ukur 9 BO bak Cuba
- AUK-003  Mistar Baja 30 cm Alat Ukur 15 baik Cuba
- AVI-001  Proyektor Portabel Perangkat Audio Visual 3 ® bak REE ers |
- AVI-002 Tripod Kamera Perangkat Audio Visual 6 B® bak Cuba
- AVI-003 Kamera Mirrorless Perangkat Audio Visual 2 B® bak Cuba
- JAR-001 Tang Crimping R45  Perangkat Jaringan 10 0 bak Cuba
- JAR-002 LAN Tester Perangkat Jaringan 5 B® bak Cuba
- JAR-003 Switch 8 Port Perangkat Jaringan 4 O bak Cuba
- PKT-001  Obeng Plus Set Perkakas Tangan 10 MO bak Cuba
Showing 1to 10 of 12 results < [| 2 >
|
@9 Request Timeline Views @@ Queries @) Models @) Gate @ Cache @ % 12x B28ME © 471ms GET /alat BB = A X


=========================================
PAGE 18
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
18

Halaman Form Alat :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_18_img_32.png] ---
v  @ UbshAist x + = o Xx
« CG © localhost8000/alat/4/edit Bg (® oven ) :
Ubah Data Alat
Kode Alat Nama Alat
AUK-001 Multimeter Digital
Kategori
Alat Ukur Vv
Stok Total Stok Tersedia Kondisi
12 10 Baik v
Deskripsi
alat ukur elektronik yang dipakai untuk mengukur tegangan listrik, arus listrik,
dan hambatan atau resistansi. Alat ini juga sering disebut sebagai AVO meter
(Ampere, Volt, Ohm) atau multitester. )
Foto
Choose File No file chosen
Se
Biarkan kosong bila tidak ingin mengganti foto.
[
29 Request Timeline Views @) Queries @ Models @ Gate @ Cache @ w 12x B28MB © 436ms GET /alat/a/edit BB = A X


=========================================
PAGE 19
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
19

3.3
 
CRUD Pengguna dan Penugasan Peran

3.3.1)
 
Buat Form Request

Pada terminal buat Request :

php artisan make:request PenggunaRequest

Buka file
 
app\Http\Requests\PenggunaRequest.php
 
:

Baris paling menarik di berkas ini adalah aturan
 
password
. Nilainya berubah tergantung keadaan:

required
 
saat menambah pengguna baru,
 
nullable
 
saat mengubah. Dengan begitu, Admin yang
hanya ingin memperbaiki nomor telepon tidak dipaksa mengetik ulang kata sandi.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_19_img_33.png] ---
"use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class PenggunaRequest extends FormRequest
{
: public function authorize(): bool
dl
12 return $this—user()—can('user.kelola');|
H
: public function rules(): array
{
$penggunayangDiubah = $this—route('pengguna');
$sedangMengubah = $penggunaYangDivbah == null;
return [
*nama’ = ['required’, ‘string’, 'max:108'],
‘username’ = [
‘required’,
‘string’,
*max:50°,
*alpha_dash’,
Rule::unigue (users, ‘username')—>ignore($penggunavangDiubah),
in
email’ = [
nullable’,
‘email’,
*max:188*,
Rule::unigue (users, ‘email')—>ignore($penggunaYangDiubah),
in
‘no_telp' = [‘nullable’, ‘string’, 'max:20'],
“passwora® = [
$sedangMengubah ? ‘nullable’ : ‘required’,
‘string’,
‘min:8",
*confirmed®,
in
‘peran’ = ['required’, 'exists:roles,name'],
*is_aktif' = [‘required’, 'boolean’],
1
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_19_img_34.png] ---
47 public function messages(): array

48 1

49 return [

56 ‘username. unique’ = ‘Nama pengguna tersebut sudah dipakai.',
51 ‘username.alpha_dash' => ‘Nama pengguna hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.®,
52 *password.confirmed' => ‘Konfirmasi kata sandi tidak cocok.',

53 ‘password. min’ = ‘Kata sandi minimal 8 Karakter.',

54 *peran. required" = ‘Peran wajib dipilin.®,

55 1

56 +

57}


=========================================
PAGE 20
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
20

Aturan
 
confirmed
 
menuntut adanya isian bernama
 
password_confirmation
 
pada form. Nama itu
wajib persis, tidak boleh diterjemahkan, karena Laravel mencarinya berdasarkan pola

namafield_confirmation
.

3.3.2)
 
Buat controller

Pada terminal buat Controller :

php artisan make:controller PenggunaController --model=User

Buka file
 
app\Http\Controllers\PenggunaController.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_20_img_35.png] ---
use App\Http\Reguests\PenggunaRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
class PenggunaController extends Controller
vi
public function index(Request $request)
~ {
$katakunci = $request—query(‘cari');
$peran = $request—query(‘peran’);
~ $daftarPengguna = User ::with('roles')
v —when($kataKunci, function ($query, $katakunci) {
~ $query—where (function ($cabang) use ($kataKunci) {
~ $cabang—where('nama', 'like', '%' . $kataKunci . '%')
—oriihere(*username’, 'like', '%' . $kataKunci . '%');
Bb;
2)
w —swhen($peran, function ($query, $peran) {
$query—role($peran) ;
2)
—orderBy(*nama’)
—paginate (18)
—withquerystring();
$daftarPeran = Role::orderBy('name')—get();
“ return view(*pengguna.index’, compact(
*daftarPengguna’,
*daftarPeran’,
*katakunci',
*peran*
D;
+
public function create()
~ {
$pengguna = new User();
$daftarPeran = Role::orderBy('name')—get();
return view('pengguna.form', compact('pengguna‘, ‘daftarPeran‘));
+


=========================================
PAGE 21
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
21

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_21_img_36.png] ---
public function store(PenggunaRequest $request)
{
$data = $request—validated();
$pengguna = User::create([
“nama’ = $datal*nama’],
‘username’ = $datal'username’],
‘email’ = $datal'email’] 22 null,
‘no_telp' = $datal'no_telp'] 2? null,
‘password’ => $datal'password'],
'is_aktif' = $datal'is_aktif'],
n;
$pengguna—ssyncRoles ([$datal *peran* 11);
return redirect()
—route('pengguna.index’)
—with('sukses', ‘Pengguna berhasil ditambahkan.');
+
public function edit(User $pengguna)
{
$daftarPeran = Role::orderBy('name')—get();
return view('pengguna.form', compact('pengguna‘, ‘daftarPeran‘));
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_21_img_37.png] ---
public function update(PenggunaRequest $request, User $pengguna)
{
$data = $request—validated();
if ($this—diriSendiri($pengguna) && ! $datal'is_aktif'l) {
return back()
—with(‘gagal’, 'Anda tidak dapat menonaktifkan akun Anda sendiri.')
SwithInput();
+
$pengguna—nana = $datal*nama’ I;
$pengguna—username = $datal ‘username’;
$pengguna—email = $datal'email’] 22 null;
$pengguna—no_telp = $datal'no_telp'] 22 null;
$pengguna—is_aktif = $datal'is_aktif'];
93 if (1 empty($datal'password'1)) ff
$pengguna—password = $datal'password'];
H
$pengguna—ssave ();
$pengguna—rsyncRoles([$datal 'peran’1]);
return redirect()
—route('pengguna.index’)
—with('sukses', ‘Data pengguna berhasil diperbarui.');
+


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_21_img_38.png] ---
public function destroy(User $pengguna)
1
if ($this—dirisendiri($pengguna)) {
return redirect()
—route(‘pengguna. index’)
—with('gagal’, 'Anda tidak dapat menghapus akun Anda sendiri.');
i:
if ($pengguna—peminjamanDiajukan()—exists()) {
return redirect()
—route('pengguna. index’)
—with('gagal’, 'Pengguna tidak dapat dihapus karena memiliki riwayat peminjaman. Nonaktifkan akunnya sebagai gantinya.');
i
$pengguna—delete();
return redirect ()
—route(*pengguna.index*)
—with('sukses’, 'Pengguna berhasil dihapus.');
}
private function diriSendiri(User $pengguna): bool
1
return $pengguna—id = auth()—>id();
¥
i


=========================================
PAGE 22
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
22

Tentang larangan diri sendiri. Pengguna tidak boleh menghapus atau menonaktifkan akunnya
sendiri. Bayangkan bila aturan ini tidak ada: satu-satunya Admin menonaktifkan akunnya, lalu
tidak ada seorang pun yang bisa mengaktifkannya kembali karena hanya Admin yang punya izin
itu. Aplikasi terkunci selamanya. Kasus seperti ini disebut lockout, dan mencegahnya jauh lebih
mudah daripada memperbaikinya.

Tentang password saat mengubah. Perhatikan blok
 
if (! empty($data['password']))
. Kalau isian
dibiarkan kosong, kolom password tidak disentuh sama sekali. Ingat juga bahwa
 
Hash::make()
 
tidak
dipanggil di mana pun
 
—
 
cast
 
'password' => 'hashed'
 
pada model User sudah menanganinya.

Tentang
 
syncRoles()
. Method ini mengganti seluruh peran pengguna dengan daftar baru. Berbeda
dengan
 
assignRole()
 
yang hanya menambahkan, sehingga bila dipakai saat mengubah data,
pengguna bisa berakhir memiliki dua peran sekaligus.

Pemeriksaan ini sebenarnya lapis kedua, karena foreign key
 
peminjaman.user_id
 
sudah memakai

restrictOnDelete()
. Bedanya, pemeriksaan di controller menghasilkan pesan yang menjelaskan

apa yang harus dilakukan sebagai gantinya
 
—
 
menonaktifkan akun. Pesan yang memberi jalan
keluar selalu lebih baik daripada pesan yang hanya melarang.

3.3.3)
 
Tambahkan relasi pada model

Method
 
destroy()
 
di atas memanggil
 
peminjamanDiajukan()
. Tambahkan relasinya di

app/Models/User.php
:

3.3.4)
 
Daftarkan route

Edit file
 
routes\web.php
 
:

Nama parameter
 
{pengguna}
 
sengaja berbeda dari nama model
 
User
. Agar Laravel tetap dapat
mencarikan datanya, nama variabel di controller harus sama:
 
User $pengguna
. Kesamaan nama
parameter route dengan nama variabel itulah yang menjadi patokannya, bukan nama modelnya.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_22_img_39.png] ---
Noo
class User extends Authenticatable
{
ooo
public function peminjamanDiajukan()
{
return $this->hasMany(Peminjaman::class, ‘user_id');
}
}


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_22_img_40.png] ---
[len
use App\Http\Controllers\PenggunaController;
Route: :middleware([ 'auth'])->group(function () {
lias
Route: :resource( 'pengguna’, PenggunaController::class)
->except(['show'])
->middleware('permission:user.kelola‘);
IH


=========================================
PAGE 23
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
23

3.3.5)
 
Buat halaman daftar

Buat
 
resources/views/pengguna/daftar.blade.php
:

Buat file
 
resources\views\pengguna\form-pencarian.blade.php
 
:

Buat file
 
resources\views\pengguna\table.blade.php
 
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_23_img_41.png] ---
(@extends (*layouts.utama')
@section(*judul', ‘Daftar Pengguna‘)
@section('konten')
+ <div class="d-flex justify-content-between align-items-center mp-3">
<h4 class="mb-0">Daftar Pengguna</hé>
<x-tombol-tambah href="{{ route(pengguna.create') }}" label="Tambah Pengguna" />
</div>
+ <div class="card">
v <div class="card-body">
@include(*pengguna.form-pencarian')
@include(*pengguna.table*)
{{ $daftarPengguna—1links() }}
</div>
</div>
@endsection


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_23_img_42.png] ---
<form method="GET" action="{{ route(’pengguna.index') }}" class="row g-2 mb-3">
<div class="col-md-4">
<input type="text" name="cari® class="form-control” placeholder="Cari nama atau nama pengguna”
value="{{ $kataKunci }}">
</div>
<div class="col-md-3">
<select name="peran” class="form-select">
<option value="">Semua Peran</option>
@foreach ($daftarPeran as $pilihanPeran)
<option valve="{{ $pilihanPeran—name }}" {{ $peran = $pilihanPeran—name > ‘selected’ : '' }>
{{ vcfirst($pilihanPeran—name) }+
</option>
@endforeach
</select>
</div>
<div class="col-auto">
<button type="submit" class="btn btn-outline-secondary”>Filter</button>
<a href="{{ route('pengguna.index') }" class="btn btn-outline-secondary">Reset</a>
</div>
</form>
Pt


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_23_img_43.png] ---
vv <div class="table-responsive">
v <table class="table table-striped align-middle">
“ <thead>
“ <tr>
<th style="width: 68px*>No</th>
<th>Nama</th>
<th>Nama Pengguna</th>
<th>Peran</th>
<th>Telepon</th>
<th>Status</th>
<th style="width: 168px">Aksi</th>
</tr>
</thead>


=========================================
PAGE 24
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
24

Tombol Hapus disembunyikan untuk akun sendiri. Ini hanya kenyamanan tampilan; penjagaan
sebenarnya tetap ada di controller, karena tombol yang disembunyikan masih bisa ditembus
dengan mengirim permintaan langsung.

3.3.6)
 
Buat halaman form

Buat
 
resources/views/pengguna/form.blade.php
:

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_24_img_44.png] ---
<tbody>
@forelse ($daftarPengguna as $nomor => $pengguna)
<tr>
<td>{{ $daftarPengguna—firstItem() + $nomor }}</td>
<td>{{ $pengguna—nama }}</ta>
<td>{{ $pengguna—username } </td>
<td>
@foreach ($pengguna—roles as $peranPengguna)
<span class="badge bg-info text-dark">
{{ ucfirst($peranPengguna—name)
</span>
@endforeach
</td>
<td>{{ $pengguna—no_telp 2: '-' }}</td>
<td>
<span class="badge bg-{{ $pengguna—is_aktif ? ‘success’ : ‘secondary’ }}">
{{ $pengguna—is_aktif ? *AKtif' : 'Nomaktif' }}
</span>
</td>
<td>
@php
$hapus = $pengguna—id == auth()—id() ? route('pengguna.destroy’, $pengguna) : null;
@endphp
<x-tombol-aksi :ubah="route('pengguna.edit’, $pengguna)” :hapus="$hapus"
pesanHapus="Yakin ingin menghapus pengguna {{ $pengguna—nama }}2" />
</td>
</tr>
@empty
<tr>
<td colspan="7" class="text-center text-muted">
Data pengguna tidak ditemukan.
</td>
</tr>
@endforelse
</tbody>
</table>
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_24_img_45.png] ---
(@extends (*layouts.utama')
@section(*judul', $pengguna—rexists ? 'Ubah Pengguna’ : 'Tambah Pengguna‘)
@section('konten')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
<div class="card-body">
<n5 class="card-title mb-4">
{{ $pengguna—exists ? 'Ubah Data Pengguna’ : 'Tambah Data Pengguna’ }
</hs>
<form method="POST"
action="{{ $pengguna—exists ? route('pengguna.update', $pengguna) : route('pengguna.store') }}">
@csrf
@if ($pengguna—exists)
@method (*PUT")
@endif


=========================================
PAGE 25
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
25

Baris
 
$pengguna->roles->first()?->name
 
memakai tanda tanya sebelum panah. Tanda itu berarti:
kalau tidak ada peran sama sekali, hasilnya kosong, bukan halaman error. Diperlukan karena pada
form tambah, objek pengguna masih baru dan belum punya peran apa pun.

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_25_img_46.png] ---
<div class="row">
<div class="col-md-6">
<x-input label="Nama Lengkap" name="nama" :value="$pengguna—nama" required />
</div>
<div class="col-md-6">
<x-input label="Nama Pengguna” name="username" :value="$pengguna—username” required />
</div>
</div>
<div class="row">
<div class="col-md-6">
<x-input label="Email" name="email" :value="$pengguna—email" type="email" required />
</div>
<div class="col-md-6">
<x-input label="Nomor Telepon" name="no_telp" :value="$pengguna—no_telp" />
</div>
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_25_img_47.png] ---
@if ($pengguna—exists)
<span class="text-muted small">(Password kosongkan bila tidak diganti)</span>
@endif
<div class="row">
<div class="col-md-6">
<x-input label="Kata Sandi" name="password" type="password" />
</div>
<div class="col-md-6">
<x-input label="Konfirmasi Kata Sandi® name="password_confirmation" type="password” />
</div>
</div>
<div class="row">
<div class="col-md-6 mb-3">
<label for="peran" class="forn-label">Peran</label>
<select class="form-select @error(‘peran') is-invalid @enderror® id="peran"
name="peran" required>
<option value="">-— Pilih Peran --</option>
@foreach ($daftarPeran as $pilihanPeran)
<option value="{{ $pilihanPeran—name }}"
4{ old('peran’, $pengguna—sroles—First()?—sname) = $pilihanPeran—sname ? ‘selected’ : '* >
{{ vcfirst($pilihanPeran—name) }}
</option>
@endforeach
</select>
@error(*peran*)
<div class="invalid-feedback">{{ $message }} </div>
@enderror
</div>
<div class="col-md-6 mb-3">
<x-select label="Aktif" name="is_aktif" :value="$pengguna—ris_aktif" placeholder="Pilih status aktif"
topsi="[['key' => 1, ‘label’ => 'Aktif'l], ['key' => 8, 'label' => 'Nonaktif']]" />
</div>
</div>


--- [IMAGE CODE SNIPPET / SCREENSHOT: page_25_img_48.png] ---
<button type="submit" class="btn btn-primary">Simpan</button>
<a href="{{ route('pengguna.index') }}" class="btn btn-secondary">Batal</a>
</form>
</div>
</div>
</div>
@endsection


=========================================
PAGE 26
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
26

3.3.7)
 
Aktifkan menu dan uji

Ganti tautan menu Pengguna di navbar menjadi
 
{{ route('pengguna.index') }}
.

Uji sebagai admin:

No
 
Percobaan
 
Hasil yang harus muncul

1
 
Buka menu Pengguna
 
3 akun hasil seeder dengan lencana peran masing-masing

2
 
Saring berdasarkan peran
petugas

Tersisa 1 baris

3
 
Tambah peminjam baru
 
Tersimpan, peran tampil sebagai lencana

4
 
Periksa kolom password di
phpMyAdmin

Berupa hash diawali $2y$, bukan teks polos

5
 
Tambah pengguna dengan
username admin

Ditolak, muncul pesan sudah dipakai

6
 
Tambah pengguna dengan
konfirmasi kata sandi berbeda

Ditolak, muncul pesan konfirmasi tidak cocok

7
 
Ubah nomor telepon tanpa
mengisi kata sandi

Berhasil, dan akun masih bisa dipakai masuk dengan kata
sandi lama

8
 
Ubah peran peminjam baru
menjadi petugas

Berhasil, lencana berubah, dan setelah masuk menunya
ikut berubah

9
 
Setel akun sendiri menjadi
Nonaktif

Ditolak, muncul pesan merah

10
 
Nonaktifkan akun peminjam
lalu coba masuk dengan akun
itu

Muncul pesan "Akun Anda dinonaktifkan"

Halaman Daftar Pengguna :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_26_img_49.png] ---
v  @ Daftar Pengguna x + - Oo x
« CG  @ localhost:8000/pengguna Bg (ous) :
Peminjaman Alat Kategori Alat Pengguna Administrato [ Keluar |
Daftar Pengguna Tambah Pengguna

Cari nama atau nama pengguna Semua Peran v | Fitter || Reset |
Ne Nama Nama Pengguna Peran Telepon Status Aksi
1 Administrator admin Admin 081200000001 Cuba
2 Petugas Laboratorium petugas  Petugas 081200000002 Cuba
3 Siswa Peminjam peminjam (Feminam) 081200000003 Cuba
@9 Request Timeline Views @) Queries @) Models @) Gate @ Cache @ % 12x B28MB © 520ms GET /pengguna 5 = A X


=========================================
PAGE 27
=========================================

ALDHI XAR : PROJECT PEMINJAMAN ALAT
 
27

Halaman Form Pengguna :

--- [IMAGE CODE SNIPPET / SCREENSHOT: page_27_img_50.png] ---
v  @ Ubah Pengguna x + - oOo x
« CG  @ localhost:8000/pengguna/2/edit tg (Bouvet) :
Peminjaman Alat Kategori Alat Pengguna Administrato [ Keluar |

Ubah Data Pengguna
Nama Lengkap Nama Pengguna
Petugas Laboratorium petugas
Email Nomor Telepon
petugas@sekolah.sch.id 081200000002
(Password kosongkan bila tidak diganti)
Kata Sandi Konfirmasi Kata Sandi
Peran Aktif
Petugas v Aktif v
@9 Request Timeline Views @)) Queries @ Models @ Gate @ Cache @ © 12x BR 28M8 © 444ms GET /pengguna/2/edit @ = A X
