<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    public function test_login_field_kosong_menampilkan_pesan_bahasa_indonesia(): void
    {
        $response = $this->post(route('login'), [
            'username' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'username' => 'Kolom nama pengguna wajib diisi.',
            'password' => 'Kolom kata sandi wajib diisi.',
        ]);
    }

    public function test_login_kredensial_salah_menampilkan_pesan_bahasa_indonesia(): void
    {
        $response = $this->post(route('login'), [
            'username' => 'admin',
            'password' => 'passwordsalah',
        ]);

        $response->assertSessionHasErrors([
            'username' => 'Nama pengguna atau kata sandi yang Anda masukkan salah.',
        ]);
    }

    public function test_login_akun_nonaktif_menampilkan_pesan_bahasa_indonesia(): void
    {
        $user = User::where('username', 'peminjam')->first();
        $user->update(['is_aktif' => false]);

        $response = $this->post(route('login'), [
            'username' => 'peminjam',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'username' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
        ]);
    }
}
