<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as KontrakLoginResponse;

class LoginResponse implements KontrakLoginResponse
{
    public function toResponse($request)
    {
        $pengguna = $request->user();

        if ($pengguna->hasRole('admin')) {
            return redirect()->route('admin.dasbor');
        }

        if ($pengguna->hasRole('petugas')) {
            return redirect()->route('petugas.dasbor');
        }

        return redirect()->route('peminjam.dasbor');
    }
}
