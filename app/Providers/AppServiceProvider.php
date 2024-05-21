<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        //* Link esp32cam 

        View::share([
            'link1' => 'http://10.10.10.121',
            'link2' => 'http://10.10.10.121',
        ]);
    }
}
