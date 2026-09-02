<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengaturanRequest;
use App\Models\LogAktivitas;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\DB;

class PengaturanController extends Controller
{
    public function form()
    {
        $pengaturan = Pengaturan::pluck('nilai', 'kunci');

        return view('pengaturan.form', compact('pengaturan'));
    }

    public function perbarui(PengaturanRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $daftarPerubahan = [];

            foreach ($data as $kunci => $nilai) {
                $baris = Pengaturan::where('kunci', $kunci)->first();

                if (! $baris || $baris->nilai == $nilai) {
                    continue;
                }

                $daftarPerubahan[] = $kunci . ': ' . $baris->nilai . ' menjadi ' . $nilai;

                $baris->update(['nilai' => $nilai]);
            }

            if (! empty($daftarPerubahan)) {
                LogAktivitas::create([
                    'user_id'      => auth()->id(),
                    'aksi'         => 'ubah_pengaturan',
                    'tabel_tujuan' => 'pengaturan',
                    'deskripsi'    => 'Mengubah pengaturan — '
                        . implode('; ', $daftarPerubahan),
                    'ip_address'   => request()->ip(),
                ]);
            }
        });

        return redirect()
            ->route('pengaturan.form')
            ->with('sukses', 'Pengaturan berhasil disimpan.');
    }
}
