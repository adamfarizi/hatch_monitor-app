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
            //! Wifi Hotspot Area 
            'link1' => 'http://10.10.10.100',
            'link2' => 'http://10.10.10.100/saved-photo',

            //! Wifi Lainnya 
            // 'link1' => 'http://192.168.1.150',
            // 'link2' => 'http://192.168.1.150/saved-photo',
        ]);
    }
}
