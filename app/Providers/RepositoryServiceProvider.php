<?php

namespace App\Providers;

use App\Interfaces\AnalyticRepositoryInterface;
use App\Repositories\AnalyticRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AnalyticRepositoryInterface::class, AnalyticRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
