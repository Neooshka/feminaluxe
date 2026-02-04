<?php

namespace App\Providers;

use App\Domains\Portfolio\Services\PortfolioSeoService;
use Illuminate\Support\ServiceProvider;

class PortfolioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PortfolioSeoService::class
        );
    }
}