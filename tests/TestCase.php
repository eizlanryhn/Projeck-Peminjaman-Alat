<?php

namespace Tests;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Jalankan seeder setelah migrate agar peran & izin tersedia.
     */
    protected bool $seed = true;
    protected string $seeder = DatabaseSeeder::class;
}
