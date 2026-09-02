@php use App\Models\Pengaturan; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'dejavu sans', sans-serif; font-size: 11px; margin: 0; padding: 0 20px; }
        h2.kop { text-align: center; font-size: 14px; margin-bottom: 2px; }
        p.kop  { text-align: center; font-size: 10px; margin: 0 0 8px 0; }
        hr.kop { border: 1.5px solid #000; margin-bottom: 10px; }
        h3.judul { text-align: center; font-size: 12px; margin: 0 0 6px 0; text-transform: uppercase; }
        p.sub    { text-align: center; font-size: 10px; margin: 0 0 10px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #555; padding: 4px 6px; font-size: 10px; }
        th { background-color: #d9d9d9; text-align: center; }
        td.num { text-align: right; }
        td.ctr { text-align: center; }
        tfoot tr td { font-weight: bold; background-color: #efefef; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px;
                  text-align: center; color: #888; border-top: 1px solid #ccc; padding: 4px 20px; }
    </style>
</head>
<body>
    <h2 class="kop">{{ Pengaturan::ambil('nama_sekolah', 'Nama Sekolah') }}</h2>
    <p class="kop">{{ Pengaturan::ambil('alamat_sekolah', 'Alamat Sekolah') }}</p>
    <hr class="kop">

    @yield('isi')

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->nama }} &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
