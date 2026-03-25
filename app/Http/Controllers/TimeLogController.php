<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get today's active log if any
        $activeLog = $user->timeLogs()
            ->whereDate('date', Carbon::today())
            ->whereNull('time_out')
            ->latest()
            ->first();

        // Get logs for the current year
        $endDate = Carbon::today();
        $startDate = Carbon::today()->startOfYear();
        
        $logs = $user->timeLogs()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get();

        // Prepare chart data
        $chartLabels = [];
        $chartData = [];
        
        // Clone startDate to avoid modifying it if we were using it elsewhere
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $displayDate = $currentDate->format('M d');
            
            // Filter logs for this specific date
            $dayTotal = $logs->filter(function ($log) use ($dateString) {
                return $log->date->format('Y-m-d') === $dateString;
            })->sum('total_hours');
            
            $chartLabels[] = $displayDate;
            $chartData[] = $dayTotal;
            
            $currentDate->addDay();
        }

        // Calculate total hours ever
        $totalSummary = $user->timeLogs()->sum('total_hours');
        if ($activeLog) {
            // Add current session duration
            $start = Carbon::parse($activeLog->time_in);
            $now = Carbon::now();
            $diff = $start->diffInMinutes($now) / 60;
            $totalSummary += $diff;
        }

        return view('dashboard', compact('activeLog', 'chartLabels', 'chartData', 'totalSummary', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if already clocked in
        $activeLog = $user->timeLogs()
            ->whereNull('time_out')
            ->first();

        if ($activeLog) {
            return back()->withErrors(['message' => 'You are already clocked in.']);
        }

        $user->timeLogs()->create([
            'date' => Carbon::today(),
            'time_in' => Carbon::now(),
            'status' => 'active',
        ]);

        return back()->with('success', 'Clocked in successfully.');
    }

    public function update(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->user_id !== Auth::id()) {
            abort(403);
        }

        if ($timeLog->time_out) {
             return back()->withErrors(['message' => 'This session is already completed.']);
        }

        $now = Carbon::now();
        $start = Carbon::parse($timeLog->time_in);
        $diffMinutes = $start->diffInMinutes($now);
        $totalHours = round($diffMinutes / 60, 2);

        $timeLog->update([
            'time_out' => $now,
            'total_hours' => $totalHours,
            'status' => 'completed',
            'notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Clocked out successfully.');
    }

    public function history()
    {
        $logs = Auth::user()->timeLogs()->latest()->paginate(10);
        return view('logs', compact('logs'));
    }
}
