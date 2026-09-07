<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KoreksiPengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pengembalian.kelola');
    }

    public function rules(): array
    {
        return [
            'denda_kerusakan' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'catatan'         => ['nullable', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'denda_kerusakan.required' => 'Denda kerusakan wajib diisi (isi 0 jika tidak ada kerusakan).',
            'denda_kerusakan.numeric'  => 'Denda kerusakan harus berupa angka.',
            'denda_kerusakan.min'      => 'Denda kerusakan tidak boleh bernilai negatif.',
            'denda_kerusakan.max'      => 'Denda kerusakan melebihi batas wajar.',
            'catatan.min'              => 'Catatan minimal 5 karakter.',
            'catatan.max'              => 'Catatan maksimal 500 karakter.',
        ];
    }
}
