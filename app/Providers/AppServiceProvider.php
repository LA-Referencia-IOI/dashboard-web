<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // The deployer derives APP_URL from the route that publishes this
        // application (for example https://dark.example.org/admin).  Make it
        // the single source of truth for route(), url() and asset(), whether
        // the request came through a reverse proxy or an Artisan command.
        $publicUrl = rtrim((string) config('app.url'), '/');
        if ($publicUrl !== '') {
            URL::forceRootUrl($publicUrl);
            URL::forceScheme(parse_url($publicUrl, PHP_URL_SCHEME) ?: 'http');
        }
    }
}
