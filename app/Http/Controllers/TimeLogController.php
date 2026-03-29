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
        
        // Handle manual time out if provided
        if ($request->filled('manual_time_out')) {
            try {
                // Combine the original log date with the manual time
                $stopTime = Carbon::parse($timeLog->date->format('Y-m-d') . ' ' . $request->input('manual_time_out'));
                
                // If manual time is before start time, it might be the next day or just an error
                // For now, we assume same day as the log date
                if ($stopTime->lt(Carbon::parse($timeLog->date->format('Y-m-d') . ' ' . $timeLog->time_in))) {
                    return back()->withErrors(['message' => 'Manual clock-out time cannot be before clock-in time.']);
                }
            } catch (\Exception $e) {
                return back()->withErrors(['message' => 'Invalid time format provided.']);
            }
        } else {
            $stopTime = $now;
        }
        
        // Calculate hours based on date and time
        $startDateTime = Carbon::parse($timeLog->date->format('Y-m-d') . ' ' . $timeLog->time_in);
        $diffMinutes = $startDateTime->diffInMinutes($stopTime);
        $totalHours = round($diffMinutes / 60, 2);

        $timeLog->update([
            'time_out' => $stopTime,
            'total_hours' => $totalHours,
            'status' => 'completed',
            'notes' => $request->input('notes', $request->filled('manual_time_out') ? 'Manual clock out.' : 'Clocked out successfully.'),
        ]);

        return back()->with('success', 'Clocked out successfully.');
    }

    public function history()
    {
        $logs = Auth::user()->timeLogs()
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->paginate(10);
            
        return view('logs', compact('logs'));
    }

    public function export()
    {
        $logs = Auth::user()->timeLogs()
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->get();

        $filename = 'time-logs-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Time In', 'Time Out', 'Total Hours', 'Notes', 'Status']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->date->format('Y-m-d'),
                    Carbon::parse($log->time_in)->format('H:i'),
                    $log->time_out ? Carbon::parse($log->time_out)->format('H:i') : 'N/A',
                    $log->total_hours,
                    $log->notes,
                    $log->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
