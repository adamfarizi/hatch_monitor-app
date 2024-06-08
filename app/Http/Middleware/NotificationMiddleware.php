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
        $scanNotifications = Penetasan::whereDate('batas_scan', $today)->get();
        $completeNotifications = Penetasan::whereDate('tanggal_selesai', $today)->get();

        // Bagikan notifikasi ke semua view
        View::share('scanNotifications', $scanNotifications);
        View::share('completeNotifications', $completeNotifications);

        return $next($request);
    }
}

