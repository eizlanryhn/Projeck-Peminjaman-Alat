<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('kategori.kelola');
    }

    public function rules(): array
    {
        $kategoriYangDiubah = $this->route('kategori');

        return [
            'nama' => [
                'required',
                'string',
                'min:2',
                'max:160',
                Rule::unique('kategori', 'nama')->ignore($kategoriYangDiubah),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1600'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.min'      => 'Nama kategori minimal 2 karakter.',
            'nama.max'      => 'Nama kategori maksimal 160 karakter.',
            'nama.unique'   => 'Nama kategori tersebut sudah terdaftar.',
            'deskripsi.max' => 'Deskripsi kategori maksimal 1.600 karakter.',
        ];
    }
}
