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
            'keperluan'         => ['nullable', 'string', 'max:500'],
            'alasan_tolak'      => ['nullable', 'string', 'max:500'],
        ];
    }
}
