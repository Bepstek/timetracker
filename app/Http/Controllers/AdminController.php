<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard(Request $request): View
    {
        $totalUsers = User::count();
        $studentUsers = User::where('role', 'student')->count();
        $totalHours = TimeLog::where('status', 'completed')->sum('total_hours');
        $thisMonthHours = TimeLog::where('status', 'completed')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('total_hours');

        $recentLogs = TimeLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $userStats = User::where('role', 'student')
            ->with(['timeLogs' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'totalHours' => $user->getHoursSummary(),
                    'thisMonthHours' => $user->getHoursSummary(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ),
                ];
            });

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'studentUsers' => $studentUsers,
            'totalHours' => $totalHours,
            'thisMonthHours' => $thisMonthHours,
            'recentLogs' => $recentLogs,
            'userStats' => $userStats,
        ]);
    }

    /**
     * Display all time logs with filtering
     */
    public function logs(Request $request): View
    {
        $query = TimeLog::with('user')->orderBy('date', 'desc')->orderBy('time_in', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20);
        $users = User::where('role', 'student')->get();

        return view('admin.logs', [
            'logs' => $logs,
            'users' => $users,
            'filters' => [
                'user_id' => $request->user_id,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'status' => $request->status,
            ],
        ]);
    }
}

