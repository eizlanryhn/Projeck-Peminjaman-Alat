<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengaturanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pengaturan.kelola');
    }

    public function rules(): array
    {
        return [
            'tarif_denda_harian'  => ['required', 'numeric', 'min:0', 'max:9999999'],
            'default_hari_pinjam' => ['required', 'integer', 'min:1', 'max:365', 'lte:maks_hari_pinjam'],
            'maks_hari_pinjam'    => ['required', 'integer', 'min:1', 'max:365'],
            'nama_sekolah'        => ['required', 'string', 'min:5', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'tarif_denda_harian.required'  => 'Tarif denda harian wajib diisi.',
            'tarif_denda_harian.numeric'   => 'Tarif denda harian harus berupa angka.',
            'tarif_denda_harian.min'       => 'Tarif denda harian tidak boleh bernilai negatif.',
            'tarif_denda_harian.max'       => 'Tarif denda harian melebihi batas wajar.',
            'default_hari_pinjam.required' => 'Durasi pinjam bawaan wajib diisi.',
            'default_hari_pinjam.integer'  => 'Durasi pinjam bawaan harus berupa bilangan bulat.',
            'default_hari_pinjam.min'      => 'Durasi pinjam bawaan minimal 1 hari.',
            'default_hari_pinjam.max'      => 'Durasi pinjam bawaan tidak boleh melebihi 365 hari.',
            'default_hari_pinjam.lte'      => 'Durasi bawaan tidak boleh melebihi durasi maksimal.',
            'maks_hari_pinjam.required'    => 'Durasi pinjam maksimal wajib diisi.',
            'maks_hari_pinjam.integer'     => 'Durasi pinjam maksimal harus berupa bilangan bulat.',
            'maks_hari_pinjam.min'         => 'Durasi pinjam maksimal minimal 1 hari.',
            'maks_hari_pinjam.max'         => 'Durasi pinjam maksimal tidak boleh melebihi 365 hari.',
            'nama_sekolah.required'        => 'Nama sekolah wajib diisi.',
            'nama_sekolah.min'             => 'Nama sekolah minimal 5 karakter.',
            'nama_sekolah.max'             => 'Nama sekolah maksimal 150 karakter.',
        ];
    }
}
