<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Date;

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
        $this->configureDefault();
    }

    protected function configureDefault(): void
    {
        Date::use(CarbonImmutable::class);
    }
}
