<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TimeLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('company');
        
        // Get today's active log if any
        $activeLog = $user->timeLogs()
            ->whereDate('date', Carbon::today())
            ->whereNull('time_out')
            ->latest()
            ->first();

        // Get logs for the current year *only* if they match the student's *current* company assignment
        $endDate = Carbon::today();
        $startDate = Carbon::today()->startOfYear();
        
        $logsQuery = $user->timeLogs()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc');

        if ($user->company_id) {
            // Strict match: the log's company MUST match the user's current company
            $logsQuery->where('company_id', $user->company_id);
        } else {
            // If the user has no company assigned, don't show any logs on the dashboard
            $logsQuery->whereRaw('1 = 0');
        }

        $logs = $logsQuery->get();
        $recentLogs = $logs->take(5);

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

        // Calculate total hours for current company, subtracting 1 hour for lunch from each log > 1 hour
        $totalSummaryQuery = $user->timeLogs();
        if ($user->company_id) {
            $totalSummaryQuery->where('company_id', $user->company_id);
        } else {
            $totalSummaryQuery->whereRaw('1 = 0');
        }
        
        $logsForSummary = $totalSummaryQuery->get();
        $totalSummary = $logsForSummary->reduce(function ($carry, $log) {
            $hours = $log->total_hours ?? 0;
            // Subtract 1 hour if the log is > 1 hour, otherwise use the hours as is.
            $renderedHours = ($hours > 1) ? $hours - 1 : $hours;
            return $carry + $renderedHours;
        }, 0);

        if ($activeLog && $user->company_id && $activeLog->company_id === $user->company_id) {
            // Add current session duration
            // Use only the time part if time_in is already a Carbon instance or formatted string
            $timeIn = $activeLog->time_in;
            if ($timeIn instanceof \Carbon\Carbon) {
                $timeIn = $timeIn->format('H:i:s');
            } elseif (strlen($timeIn) > 8) {
                // If it's a full datetime string like "2026-03-30 11:40:05", extract only the time
                $timeIn = date('H:i:s', strtotime($timeIn));
            }

            $start = \Carbon\Carbon::parse($activeLog->date->format('Y-m-d') . ' ' . $timeIn);
            $now = \Carbon\Carbon::now();
            $diff = $start->diffInMinutes($now) / 60;

            // Also apply the lunch-hour deduction to the live timer if it's been running for more than an hour
            $renderedDiff = ($diff > 1) ? $diff - 1 : $diff;
            $totalSummary += $renderedDiff;
        }

        // Calculate days left
        $requiredHours = 486.00;
        $remainingHours = max(0, $requiredHours - $totalSummary);
        
        // Calculate average hours per day (only for days worked in current company)
        $daysWorkedQuery = $user->timeLogs()->distinct('date');
        if ($user->company_id) {
            $daysWorkedQuery->where('company_id', $user->company_id);
        } else {
            $daysWorkedQuery->whereRaw('1 = 0');
        }
        $daysWorked = $daysWorkedQuery->count();

        $avgSumQuery = $user->timeLogs();
        if ($user->company_id) {
            $avgSumQuery->where('company_id', $user->company_id);
        } else {
            $avgSumQuery->whereRaw('1 = 0');
        }
        $logsForAverage = $avgSumQuery->get();
        $totalRenderedHoursForAvg = $logsForAverage->reduce(function ($carry, $log) {
            $hours = $log->total_hours ?? 0;
            $renderedHours = ($hours > 1) ? $hours - 1 : $hours;
            return $carry + $renderedHours;
        }, 0);
        $averageHoursPerDay = $daysWorked > 0 ? ($totalRenderedHoursForAvg / $daysWorked) : 0;
        
        // Estimate days left (using 8 hours as fallback if no average yet)
        $dailyRate = $averageHoursPerDay > 0 ? $averageHoursPerDay : 8;
        $daysLeft = $dailyRate > 0 ? ceil($remainingHours / $dailyRate) : 0;

        return view('dashboard', compact('activeLog', 'chartLabels', 'chartData', 'totalSummary', 'user', 'daysLeft', 'averageHoursPerDay', 'recentLogs'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if student is assigned to a company
        if (!$user->company_id) {
            return back()->withErrors(['message' => 'You cannot clock in yet. Please wait for an administrator to assign you to an OJT Room.']);
        }

        // Check if already clocked in
        $activeLog = $user->timeLogs()
            ->whereNull('time_out')
            ->first();

        if ($activeLog) {
            return back()->withErrors(['message' => 'You are already clocked in.']);
        }

        // Prevent multiple logs in the same day (Clock-in attempt)
        $todayLog = $user->timeLogs()
            ->whereDate('date', Carbon::today())
            ->where('company_id', $user->company_id)
            ->exists();

        if ($todayLog) {
            return back()->withErrors(['message' => 'You have already recorded a log for today. Only one session per day is allowed.']);
        }

        $user->timeLogs()->create([
            'company_id' => $user->company_id,
            'date' => Carbon::today(),
            'time_in' => Carbon::now(),
            'status' => 'active',
        ]);

        return back()->with('success', 'Clocked in successfully.');
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'time_in' => 'required',
            'time_out' => 'required|after:time_in',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check for existing log on this date
        $existingLog = $user->timeLogs()
            ->whereDate('date', $request->date)
            ->where('company_id', $user->company_id)
            ->exists();

        if ($existingLog) {
            return back()->withErrors(['message' => 'You already have a time log recorded for ' . Carbon::parse($request->date)->format('M d, Y') . '.']);
        }
        
        $timeIn = Carbon::parse($request->date . ' ' . $request->time_in);
        $timeOut = Carbon::parse($request->date . ' ' . $request->time_out);
        
        $diffMinutes = $timeIn->diffInMinutes($timeOut);
        $totalHours = round($diffMinutes / 60, 2);

        $user->timeLogs()->create([
            'company_id' => $user->company_id,
            'date' => $request->date,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'total_hours' => $totalHours,
            'notes' => $request->notes,
            'status' => 'completed',
        ]);

        return back()->with('success', 'Manual time log added successfully.');
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
                $timeInValue = $timeLog->time_in;
                if ($timeInValue instanceof Carbon) {
                    $timeInValue = $timeInValue->format('H:i:s');
                } elseif (strlen($timeInValue) > 8) {
                    $timeInValue = date('H:i:s', strtotime($timeInValue));
                }

                $stopTime = Carbon::parse($timeLog->date->format('Y-m-d') . ' ' . $request->input('manual_time_out'));
                
                // If manual time is before start time, it might be the next day or just an error
                // For now, we assume same day as the log date
                if ($stopTime->lt(Carbon::parse($timeLog->date->format('Y-m-d') . ' ' . $timeInValue))) {
                    return back()->withErrors(['message' => 'Manual clock-out time cannot be before clock-in time.']);
                }
            } catch (\Exception $e) {
                return back()->withErrors(['message' => 'Invalid time format provided.']);
            }
        } else {
            $stopTime = $now;
        }
        
        // Calculate hours based on date and time
        $timeInValue = $timeLog->time_in;
        if ($timeInValue instanceof Carbon) {
            $timeInValue = $timeInValue->format('H:i:s');
        } elseif (strlen($timeInValue) > 8) {
            $timeInValue = date('H:i:s', strtotime($timeInValue));
        }

        $startDateTime = Carbon::parse($timeLog->date->format('Y-m-d') . ' ' . $timeInValue);
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
        $user = Auth::user();
        
        $query = $user->timeLogs()
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc');

        if ($user->company_id) {
            $query->where('company_id', $user->company_id);
        } else {
            // Student with no company sees no history for data isolation
            $query->whereRaw('1 = 0');
        }

        $logs = $query->paginate(10);
            
        return view('logs', compact('logs'));
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'excel');

        if ($type === 'pdf') {
            return $this->exportPDF();
        }

        return $this->exportExcel();
    }

    private function exportPDF()
    {
        $user = Auth::user();
        
        $query = $user->timeLogs()
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc');

        if ($user->company_id) {
            $query->where('company_id', $user->company_id);
            $company = $user->company;
        } else {
            $query->whereRaw('1 = 0');
            $company = null;
        }

        $logs = $query->get();

        // Calculate totals
        $totalRenderedHours = $logs->reduce(function ($carry, $log) {
            $hours = $log->total_hours ?? 0;
            $renderedHours = ($hours > 1) ? $hours - 1 : $hours;
            return $carry + $renderedHours;
        }, 0);

        $html = view('exports.pdf', compact('user', 'logs', 'company', 'totalRenderedHours'))->render();
        
        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        return $pdf->download('time-logs-' . now()->format('Y-m-d') . '.pdf');
    }

    private function exportExcel()
    {
        $user = Auth::user();
        
        $query = $user->timeLogs()
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc');

        if ($user->company_id) {
            $query->where('company_id', $user->company_id);
            $company = $user->company;
        } else {
            $query->whereRaw('1 = 0');
            $company = null;
        }

        $logs = $query->get();

        // Calculate totals
        $totalHours = $logs->sum('total_hours');
        $totalRenderedHours = $logs->reduce(function ($carry, $log) {
            $hours = $log->total_hours ?? 0;
            $renderedHours = ($hours > 1) ? $hours - 1 : $hours;
            return $carry + $renderedHours;
        }, 0);

        $html = view('exports.excel', compact('user', 'logs', 'company', 'totalHours', 'totalRenderedHours'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="time-logs-' . now()->format('Y-m-d') . '.xls"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
