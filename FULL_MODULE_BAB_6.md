# FULL MODULE BAB 6

Total Pages: 18



--- PAGE 1 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   1  BAB VI MODUL PENDUKUNG  6.1   Log Aktivitas: Listener, Observer, dan Trigger  6.1.1)   Buat listener logout  Login sudah dicatat, tetapi logout belum. Buat berkas   app/Listeners/CatatLogout.php :  Daftarkan di   app/Providers/AppServiceProvider.php , di dalam method   boot() :  Event   Logout   dilepaskan Laravel sendiri setiap kali session autentikasi dihapus, termasuk oleh Fortify. Anda tidak perlu menyentuh kode logout sama sekali.  Periksa pendaftarannya:  php artisan event:list --event=Logout

--- PAGE 2 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   2  6.1.2)   Buat observer untuk data master  Buat berkas   app/Observers/LogObserver.php :  Satu observer dipakai untuk tiga model sekaligus. Karena parameternya bertipe Model   —   bukan  Kategori   atau   Alat   secara khusus   —   kelas ini dapat menerima model apa pun. Nama tabel diambil lewat   getTable() , dan nama datanya lewat method   namaData()   yang mencoba beberapa kolom secara berurutan.  Penjagaan   if (! auth()->check())   wajib ada. Tanpa itu, php artisan db:seed akan gagal karena  auth()->id()   bernilai kosong saat perintah dijalankan dari terminal.

--- PAGE 3 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   3  Daftarkan ketiga model di   AppServiceProvider::boot() :  Model transaksi   —   Peminjaman ,   DetailPeminjaman ,   Pengembalian   —   sengaja tidak didaftarkan. Ketiganya sudah dicatat lewat kode manual di kelas layanan dan lewat trigger, dengan deskripsi yang jauh lebih bermakna. Mendaftarkannya di sini hanya akan menghasilkan catatan ganda.  6.1.3)   Buat trigger pemberian peran  Pada terminal buatkan migration :  php artisan make:migration create_trigger_peran  Edit file   database\migrations\..._create_trigger_peran.php   :

--- PAGE 4 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   4  Satu keterbatasan yang perlu Anda pahami dan jelaskan saat sidang. Trigger berjalan di dalam basis data, dan basis data tidak tahu siapa yang sedang masuk ke aplikasi. Karena itu kolom   user_id   diisi id pengguna yang menerima peran, bukan admin yang memberikannya. Deskripsinya dibuat cukup jelas agar tetap dapat ditelusuri.  Kalau Anda ingin mencatat siapa pemberinya, hal itu harus dikerjakan di lapisan aplikasi   —  misalnya di   PenggunaController . Trigger ini dibuat untuk memenuhi butir penilaian soal tentang trigger, dan cukup pada perannya itu.  Jalankan   php artisan migrate   lalu periksa tab Triggers di phpMyAdmin. Sekarang harus ada tiga trigger terdaftar.  6.1.4)   Buat controller log  Tambahkan cast dan relasi pada   app/Models/LogAktivitas.php :  Pada terminal buat controller :  php artisan make:controller LogAktivitasController  Edit file   app\Http\Controllers\LogAktivitasController.php   :

--- PAGE 5 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   5  Pilihan aksi pada saringan diambil langsung dari isi tabel dengan   distinct() , bukan ditulis sebagai daftar tetap. Dengan begitu, setiap jenis aksi baru yang muncul di kemudian hari otomatis ikut tersedia sebagai pilihan.  Halaman ini memakai   paginate(20) , bukan 10, karena baris log jauh lebih banyak dan lebih pendek daripada data lain.  6.1.5)   Daftarkan route dan menu  Edit file   routes\web.php   :  Tambahkan menu di Navbar, di dalam blok   @can   yang sesuai:

--- PAGE 6 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   6  6.1.6)   Buat halaman log  Buat   resources/views/log/daftar.blade.php :  Buat   resources\views\log\form-search.blade.php   :

--- PAGE 7 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   7  Buat   resources\views\log\tabel-log.blade.php   :  Kolom Pengguna memakai   ?? 'Tidak dikenal'   karena   user_id   boleh kosong pada baris percobaan login yang gagal. Tanpa penjagaan itu, halaman akan gagal dimuat begitu ada satu baris seperti itu.  6.1.7)   Uji  Sebagai admin, buka menu Log Aktivitas, lalu kerjakan berbagai aktivitas dan periksa apakah semuanya tercatat:  No   Aktivitas   Aksi yang harus muncul  1   Masuk sebagai admin   login  2   Keluar lalu masuk lagi   logout  3   Masuk dengan password salah   login_gagal  4   Tambah kategori baru   create pada tabel kategori  5   Ubah data alat   update pada tabel alat  6   Hapus kategori kosong   delete pada tabel kategori  7   Tambah pengguna baru dengan peran   create dan beri_peran  8   Petugas menyetujui peminjaman   setujui  9   Petugas memverifikasi pengembalian   verifikasi_kembali

--- PAGE 8 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   8  Halaman Log Aktivitas :

--- PAGE 9 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   9  6.2   Tiga Laporan PDF dengan dompdf  6.2.1)   Siapkan konfigurasi dompdf  Pada terminal publish provide :  php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"  Buka   config/dompdf.php , cari bagian   defines , lalu pastikan:  'default_font' => 'dejavu sans',  Font bawaan dompdf tidak memuat sebagian karakter dan sering membuat tanda titik pemisah ribuan tercetak berantakan. DejaVu Sans sudah disertakan dalam paket, jadi tidak perlu diunduh.  6.2.2)   Buat layout PDF  Buat   resources/views/laporan/layout.blade.php :  Nama sekolah dikirim sebagai variabel, dibaca dari tabel   pengaturan . Bila sekolah lain memakai aplikasi ini, cukup mengubah satu baris di halaman Pengaturan   —   tidak perlu menyentuh berkas ini sama sekali.  Catatan kaki memakai   position: fixed , yang pada dompdf berarti tercetak di setiap halaman. Ini penting untuk laporan yang panjangnya lebih dari satu lembar.  6.2.3)   Buat controller laporan  Pada terminal buat controller :  php artisan make:controller LaporanController

--- PAGE 10 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   10  Edit   app\Http\Controllers\LaporanController.php   :

--- PAGE 11 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   11  Method   stream()   membuka PDF langsung di tab peramban. Bila Anda ingin berkasnya langsung terunduh, ganti dengan   download('nama-berkas.pdf') . Untuk keperluan pengujian,   stream()   lebih praktis karena hasilnya terlihat tanpa perlu membuka folder unduhan.  Dua laporan pertama memakai orientasi   landscape   karena kolomnya banyak. Laporan stok memakai   portrait   karena kolomnya sedikit.  Perhatikan   with([...])   pada setiap query. Pada laporan, kelalaian eager loading jauh lebih terasa daripada di halaman biasa: laporan 200 baris tanpa   with()   bisa menghasilkan ratusan query dan membuat pembuatan PDF terasa menggantung.  6.2.4)   Daftarkan route dan menu  Edit   routes\web.php   :

--- PAGE 12 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   12  Tambahkan menu di navbar :  6.2.5)   Buat halaman pemilihan laporan  Buat   resources/views/laporan/form.blade.php :  Buat   resources\views\laporan\form-rpt-01.blade.php   :

--- PAGE 13 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   13  Buat   resources\views\laporan\form-rpt-02.blade.php   :  Buat   resources\views\laporan\form-rpt-03.blade.php   :

--- PAGE 14 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   14  6.2.6)   Buat tampilan RPT-01 (Report)  Buat   resources/views/laporan/peminjaman.blade.php :  6.2.7)   Buat tampilan RPT-02 dan RPT-03  Buat   resources/views/laporan/pengembalian.blade.php :

--- PAGE 15 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   15  Buat   resources/views/laporan/stok.blade.php :

--- PAGE 16 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   16  Kolom "Dipinjam" tidak disimpan di basis data, melainkan dihitung sebagai selisih   stok   dikurangi  stok_tersedia . Menyimpan angka yang bisa dihitung dari dua kolom lain hanya membuka peluang ketiganya menjadi tidak cocok.  6.2.8)   Uji  Masuk sebagai   petugas :  No   Percobaan   Hasil yang harus muncul  1   Buka menu Laporan   Tiga kartu laporan tampil  2   Cetak RPT-01 untuk bulan berjalan   PDF terbuka di tab baru, kop memuat nama sekolah  3   Cetak RPT-01 dengan status Selesai   Hanya peminjaman selesai yang tampil  4   Cetak RPT-02   Angka denda sama persis dengan halaman rincian JS-15  5   Periksa baris Total Denda Terkumpul   Sama dengan jumlah kolom Total Denda  6   Cetak RPT-03 semua kategori   12 alat tampil, kolom Dipinjam terisi benar  7   Cetak RPT-03 satu kategori   Hanya alat kategori itu, kop menyebut nama kategorinya  8   Cetak periode kosong   PDF tetap terbuka dengan keterangan tidak ada data  9   Isi tanggal akhir lebih awal dari tanggal awal   Ditolak validasi  10   Masuk sebagai admin, buka /laporan   HTTP 403  Percobaan nomor 8 penting: laporan kosong harus tetap menghasilkan PDF yang rapi, bukan halaman error. Penguji kerap mencobanya.  Percobaan nomor 10 kembali menegaskan pembagian pada matriks hak akses   —   pencetakan laporan adalah wewenang Petugas, bukan Admin.  Simpan ketiga berkas PDF hasil pengujian ke   dokumentasi/lampiran/   untuk dilampirkan pada laporan akhir.

--- PAGE 17 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   17  Halaman Form Laporan :  Halaman PDF Laporan Peminjaman (RPT-01):

--- PAGE 18 ---
ALDHI XAR : PROJECT PEMINJAMAN ALAT   18  Halaman PDF Laporan Pengembalian (RPT-02):  Halaman PDF Laporan Rekapitulasi Stok Alat (RPT-03):