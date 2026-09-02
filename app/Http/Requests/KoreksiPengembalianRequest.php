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
            'denda_kerusakan' => ['required', 'numeric', 'min:0'],
            'catatan'         => ['nullable', 'string', 'max:500'],
        ];
    }
}
