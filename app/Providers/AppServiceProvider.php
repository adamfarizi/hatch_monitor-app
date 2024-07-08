<?php

namespace App\Providers;

use App\Models\Master;
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

        $link = Master::first();

        View::share([
            //! Wifi Hotspot Area 
            // 'link1' => 'http://10.10.10.100',
            // 'link2' => 'http://10.10.10.101',
            
            //! Wifi Hotspot Area 
            // 'link1' => 'http://192.168.16.100',
            // 'link2' => 'http://192.168.16.101',

            //! Wifi Lainnya 
            // 'link1' => 'http://192.168.1.150',
            // 'link2' => 'http://192.168.1.151',

            //! Wifi Master             
            'link1' => $link->link1,
            'link2' => $link->link2,
        ]);
    }
}
