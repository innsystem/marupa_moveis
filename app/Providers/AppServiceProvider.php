<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use App\Models\Service;

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
        view()->composer('*', function ($view) {
            $getSettings = Setting::pluck('value', 'key')->toArray();
            $view->with('getSettings', $getSettings);
        });

        view()->composer('*', function ($view) {
            $getServices = Service::where('status', 1)->orderBy('sort_order')->get();
            $view->with('getServices', $getServices);
        });
    }
}
