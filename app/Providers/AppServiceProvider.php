<?php

namespace App\Providers;

use App\Listeners\CatatLogout;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Event::listen(Logout::class, CatatLogout::class);

        // Observer untuk data master
        Kategori::observe(LogObserver::class);
        Alat::observe(LogObserver::class);
        User::observe(LogObserver::class);
    }
}
