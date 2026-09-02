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
                'max:30',
                Rule::unique('alat', 'kode_alat')->ignore($alatYangDiubah),
            ],
            'nama'          => ['required', 'string', 'max:150'],
            'deskripsi'     => ['nullable', 'string', 'max:1000'],
            'stok'          => ['required', 'integer', 'min:0'],
            'stok_tersedia' => ['required', 'integer', 'min:0', 'lte:stok'],
            'kondisi'       => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat'])],
            'foto'          => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required'  => 'Kategori wajib dipilih.',
            'kode_alat.unique'      => 'Kode alat tersebut sudah terdaftar.',
            'stok_tersedia.lte'     => 'Stok tersedia tidak boleh melebihi stok total.',
            'foto.max'              => 'Ukuran foto maksimal 2 MB.',
            'foto.mimes'            => 'Foto harus berformat JPG atau PNG.',
        ];
    }
}
