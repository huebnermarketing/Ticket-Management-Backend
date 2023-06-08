<?php

namespace App\Providers;

use App\Models\CompanySettings;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class CompanyNameServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $view->with('companyName', CompanySettings::first());
        });
    }
}
