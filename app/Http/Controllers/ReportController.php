<?php

namespace App\Http\Controllers;

use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display report filter page
     */
    public function index(): View
    {
        $users = \App\Models\User::where('role', 'student')->get();

        return view('admin.reports', [
            'users' => $users,
        ]);
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,pdf',
            'user_id' => 'nullable|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $query = TimeLog::with('user')
            ->where('status', 'completed')
            ->orderBy('date', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $validated['user_id']);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $validated['date_from']);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $validated['date_to']);
        }

        $logs = $query->get();

        if ($validated['format'] === 'csv') {
            return $this->exportCsv($logs);
        } else {
            return $this->exportPdf($logs);
        }
    }

    /**
     * Export logs to CSV format
     */
    private function exportCsv($logs): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($logs) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'User Name',
                'Email',
                'Date',
                'Time In',
                'Time Out',
                'Total Hours',
                'Notes',
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->user->name,
                    $log->user->email,
                    $log->date->format('Y-m-d'),
                    $log->time_in->format('H:i:s'),
                    $log->time_out?->format('H:i:s') ?? '',
                    $log->total_hours,
                    $log->notes,
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="time_logs_' . now()->format('Y-m-d-His') . '.csv"');

        return $response;
    }

    /**
     * Export logs to PDF format
     */
    private function exportPdf($logs)
    {
        $html = view('admin.reports_pdf', ['logs' => $logs])->render();

        // For now, return simple text-based export
        // In production, use a library like TCPDF or DomPDF
        return response()->download(storage_path('logs/pdf_export.pdf'));
    }
}

