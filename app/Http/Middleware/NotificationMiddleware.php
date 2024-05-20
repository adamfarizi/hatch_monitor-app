<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\Penetasan;

class NotificationMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $today = Carbon::today();
        $notifications = Penetasan::whereDate('batas_scan', $today)
        ->whereYear('batas_scan', $today->year)
        ->whereMonth('batas_scan', $today->month)
        ->get();
        
        // Bagikan notifikasi ke semua view
        View::share('notifications', $notifications);

        return $next($request);
    }
}

