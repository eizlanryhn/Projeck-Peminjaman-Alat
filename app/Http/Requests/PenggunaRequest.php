<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenggunaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('user.kelola');
    }

    public function rules(): array
    {
        $penggunaYangDiubah = $this->route('pengguna');
        $sedangMengubah     = $penggunaYangDiubah !== null;
        return [
            'nama'     => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($penggunaYangDiubah),
            ],
            'email' => [
                'nullable',
                'email',
                'max:180',
                Rule::unique('users', 'email')->ignore($penggunaYangDiubah),
            ],
            'no_telp'  => ['nullable', 'string', 'max:20'],
            'password' => [
                $sedangMengubah ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'peran'    => ['required', 'exists:roles,name'],
            'is_aktif' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique'       => 'Nama pengguna tersebut sudah dipakai.',
            'username.alpha_dash'   => 'Nama pengguna hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
            'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'          => 'Kata sandi minimal 8 karakter.',
            'peran.required'        => 'Peran wajib dipilih.',
        ];
    }
}
