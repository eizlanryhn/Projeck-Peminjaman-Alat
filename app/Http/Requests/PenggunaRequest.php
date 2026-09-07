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
            'nama'     => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\.\,\-\']+$/u'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($penggunaYangDiubah),
            ],
            'email' => [
                'required',
                'email',
                'max:180',
                Rule::unique('users', 'email')->ignore($penggunaYangDiubah),
            ],
            'no_telp' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'digits_between:8,15',
            ],
            'password' => [
                $sedangMengubah ? 'nullable' : 'required',
                'string',
                'min:8',
                'max:64',
                'confirmed',
            ],
            'password_confirmation' => [
                $sedangMengubah ? 'nullable' : 'required',
            ],
            'peran'    => ['required', 'exists:roles,name'],
            'is_aktif' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'                  => 'Nama lengkap wajib diisi.',
            'nama.min'                       => 'Nama lengkap minimal 3 karakter.',
            'nama.max'                       => 'Nama lengkap maksimal 100 karakter.',
            'nama.regex'                     => 'Nama lengkap hanya boleh berisi huruf, spasi, dan tanda baca umum.',
            'username.required'              => 'Nama pengguna wajib diisi.',
            'username.min'                   => 'Nama pengguna minimal 3 karakter.',
            'username.max'                   => 'Nama pengguna maksimal 50 karakter.',
            'username.alpha_dash'            => 'Nama pengguna hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
            'username.unique'                => 'Nama pengguna tersebut sudah dipakai.',
            'email.required'                 => 'Email wajib diisi.',
            'email.email'                    => 'Format email tidak valid.',
            'email.max'                      => 'Email maksimal 180 karakter.',
            'email.unique'                   => 'Email tersebut sudah digunakan.',
            'no_telp.required'               => 'Nomor telepon wajib diisi.',
            'no_telp.regex'                  => 'Nomor telepon hanya boleh berisi angka.',
            'no_telp.digits_between'         => 'Nomor telepon harus berupa angka dengan panjang 8 hingga 15 digit.',
            'password.required'              => 'Kata sandi wajib diisi.',
            'password.min'                   => 'Kata sandi minimal 8 karakter.',
            'password.max'                   => 'Kata sandi maksimal 64 karakter.',
            'password.confirmed'             => 'Konfirmasi kata sandi tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
            'peran.required'                 => 'Peran wajib dipilih.',
            'peran.exists'                   => 'Peran yang dipilih tidak valid.',
            'is_aktif.required'              => 'Status aktif wajib dipilih.',
        ];
    }
}
