<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Halaman root (/) mengarahkan ke login.
     */
    public function test_halaman_root_redirect_ke_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
