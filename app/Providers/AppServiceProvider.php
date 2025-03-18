<?php

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\ClientInterface;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, function () {
            return new Client([
                'verify' => storage_path('cacert.pem'),
            ]);
        });
    }

    public function boot(): void
    {
        //
    }
}
