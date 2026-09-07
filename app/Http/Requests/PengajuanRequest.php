<?php

namespace App\Http\Requests;

use App\Models\Pengaturan;
use Illuminate\Foundation\Http\FormRequest;

class PengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('peminjaman.ajukan');
    }

    public function rules(): array
    {
        $maksHari   = (int) Pengaturan::ambil('maks_hari_pinjam', 30);
        $batasAkhir = now()->addDays($maksHari)->toDateString();

        return [
            'tgl_pinjam' => [
                'required', 'date', 'after_or_equal:today',
            ],
            'tgl_harus_kembali' => [
                'required', 'date',
                'after:tgl_pinjam',
                'before_or_equal:' . $batasAkhir,
            ],
            'keperluan' => ['nullable', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        $maksHari = (int) Pengaturan::ambil('maks_hari_pinjam', 30);

        return [
            'tgl_pinjam.required'            => 'Tanggal pinjam wajib diisi.',
            'tgl_pinjam.date'                => 'Tanggal pinjam harus berupa tanggal yang valid.',
            'tgl_pinjam.after_or_equal'      => 'Tanggal pinjam tidak boleh sebelum hari ini.',
            'tgl_harus_kembali.required'     => 'Tanggal harus kembali wajib diisi.',
            'tgl_harus_kembali.date'         => 'Tanggal harus kembali harus berupa tanggal yang valid.',
            'tgl_harus_kembali.after'        => 'Tanggal harus kembali harus setelah tanggal pinjam (minimal 1 hari).',
            'tgl_harus_kembali.before_or_equal' => "Tanggal harus kembali melebihi batas maksimum peminjaman ({$maksHari} hari).",
            'keperluan.min'                  => 'Keperluan minimal 5 karakter.',
            'keperluan.max'                  => 'Keperluan maksimal 500 karakter.',
        ];
    }
}
