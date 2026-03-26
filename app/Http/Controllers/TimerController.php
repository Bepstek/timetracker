<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimerController extends Controller
{
    /**
     * Start a new timer
     */
    public function start(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if user already has an active timer
        $activeTimer = $user->getActiveTimeLog();
        if ($activeTimer) {
            return response()->json([
                'error' => 'You already have an active timer.',
                'timer' => $activeTimer,
            ], 409);
        }

        // Create a new time log
        $timeLog = $user->timeLogs()->create([
            'date' => now()->date(),
            'time_in' => now(),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Timer started',
            'timer' => $timeLog,
        ], 201);
    }

    /**
     * Stop the active timer
     */
    public function stop(Request $request): JsonResponse
    {
        $user = $request->user();

        // Find active timer
        $activeTimer = $user->getActiveTimeLog();
        if (!$activeTimer) {
            return response()->json([
                'error' => 'No active timer found.',
            ], 404);
        }

        // Stop the timer
        $activeTimer->update([
            'time_out' => now(),
        ]);

        return response()->json([
            'message' => 'Timer stopped',
            'timer' => $activeTimer,
            'totalHours' => $activeTimer->total_hours,
        ], 200);
    }

    /**
     * Get current timer status
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $activeTimer = $user->getActiveTimeLog();

        if (!$activeTimer) {
            return response()->json([
                'status' => 'no_timer',
                'timer' => null,
            ]);
        }

        return response()->json([
            'status' => 'active',
            'timer' => $activeTimer,
            'elapsedSeconds' => now()->diffInSeconds($activeTimer->time_in),
        ]);
    }
}

