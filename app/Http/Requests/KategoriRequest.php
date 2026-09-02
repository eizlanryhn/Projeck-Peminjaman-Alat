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
                'max:160',
                Rule::unique('kategori', 'nama')
                    ->ignore($kategoriYangDiubah),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1600'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique'   => 'Nama Kategori tersebut sudah terdaftar.',
            'nama.max'      => 'Nama Kategori maksimal 160 karakter.',
        ];
    }
}
