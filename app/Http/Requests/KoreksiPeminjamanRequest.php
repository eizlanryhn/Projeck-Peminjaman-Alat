<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KoreksiPeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('peminjaman.kelola');
    }

    public function rules(): array
    {
        return [
            'tgl_pinjam'        => ['required', 'date'],
            'tgl_harus_kembali' => ['required', 'date', 'after_or_equal:tgl_pinjam'],
            'keperluan'         => ['nullable', 'string', 'min:5', 'max:500'],
            'alasan_tolak'      => ['nullable', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'tgl_pinjam.required'            => 'Tanggal pinjam wajib diisi.',
            'tgl_pinjam.date'                => 'Tanggal pinjam harus berupa tanggal yang valid.',
            'tgl_harus_kembali.required'     => 'Tanggal harus kembali wajib diisi.',
            'tgl_harus_kembali.date'         => 'Tanggal harus kembali harus berupa tanggal yang valid.',
            'tgl_harus_kembali.after_or_equal' => 'Tanggal harus kembali tidak boleh sebelum tanggal pinjam.',
            'keperluan.min'                  => 'Keperluan minimal 5 karakter.',
            'keperluan.max'                  => 'Keperluan maksimal 500 karakter.',
            'alasan_tolak.min'               => 'Alasan penolakan minimal 5 karakter.',
            'alasan_tolak.max'               => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }
}
