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
            'tarif_denda_harian'  => ['required', 'numeric', 'min:0'],
            'default_hari_pinjam' => ['required', 'integer', 'min:1', 'lte:maks_hari_pinjam'],
            'maks_hari_pinjam'    => ['required', 'integer', 'min:1', 'max:365'],
            'nama_sekolah'        => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'default_hari_pinjam.lte' => 'Durasi bawaan tidak boleh melebihi durasi maksimal.',
            'maks_hari_pinjam.max'    => 'Durasi maksimal tidak wajar, batas atasnya 365 hari.',
            'tarif_denda_harian.min'  => 'Tarif denda tidak boleh bernilai negatif.',
        ];
    }
}
