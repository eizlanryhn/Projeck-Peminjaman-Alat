<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('alat.kelola');
    }

    public function rules(): array
    {
        $alatYangDiubah = $this->route('alat');

        return [
            'kategori_id'   => ['required', 'exists:kategori,id'],
            'kode_alat'     => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[A-Za-z0-9\-]+$/',
                Rule::unique('alat', 'kode_alat')->ignore($alatYangDiubah),
            ],
            'nama'          => ['required', 'string', 'min:3', 'max:150'],
            'deskripsi'     => ['nullable', 'string', 'max:1000'],
            'stok'          => ['required', 'integer', 'min:0', 'max:9999'],
            'stok_tersedia' => ['required', 'integer', 'min:0', 'lte:stok'],
            'kondisi'       => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
            'foto'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required'   => 'Kategori wajib dipilih.',
            'kategori_id.exists'     => 'Kategori yang dipilih tidak valid.',
            'kode_alat.required'     => 'Kode alat wajib diisi.',
            'kode_alat.min'          => 'Kode alat minimal 3 karakter.',
            'kode_alat.max'          => 'Kode alat maksimal 30 karakter.',
            'kode_alat.regex'        => 'Kode alat hanya boleh berisi huruf, angka, dan tanda hubung.',
            'kode_alat.unique'       => 'Kode alat tersebut sudah terdaftar.',
            'nama.required'          => 'Nama alat wajib diisi.',
            'nama.min'               => 'Nama alat minimal 3 karakter.',
            'nama.max'               => 'Nama alat maksimal 150 karakter.',
            'deskripsi.max'          => 'Deskripsi maksimal 1.000 karakter.',
            'stok.required'          => 'Stok total wajib diisi.',
            'stok.integer'           => 'Stok total harus berupa bilangan bulat.',
            'stok.min'               => 'Stok total tidak boleh negatif.',
            'stok.max'               => 'Stok total tidak boleh melebihi 9.999 unit.',
            'stok_tersedia.required' => 'Stok tersedia wajib diisi.',
            'stok_tersedia.integer'  => 'Stok tersedia harus berupa bilangan bulat.',
            'stok_tersedia.min'      => 'Stok tersedia tidak boleh negatif.',
            'stok_tersedia.lte'      => 'Stok tersedia tidak boleh melebihi stok total.',
            'kondisi.required'       => 'Kondisi alat wajib dipilih.',
            'kondisi.in'             => 'Kondisi alat tidak valid.',
            'foto.image'             => 'File harus berupa gambar.',
            'foto.mimes'             => 'Foto harus berformat JPG atau PNG.',
            'foto.max'               => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
