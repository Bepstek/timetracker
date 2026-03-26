<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the user's dashboard
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $activeTimer = $user->getActiveTimeLog();
        $todayHours = $user->getTodayHours();
        $totalHours = $user->getHoursSummary();

        // Get recent time logs (last 5)
        $recentLogs = $user->timeLogs()
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'activeTimer' => $activeTimer,
            'todayHours' => $todayHours,
            'totalHours' => $totalHours,
            'recentLogs' => $recentLogs,
        ]);
    }
}

