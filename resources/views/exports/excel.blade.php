<?xml version="1.0" encoding="UTF-8"?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Time Logs Report</title>
    <style>
        body {
            font-family: Calibri, Arial, sans-serif;
            font-size: 11pt;
            color: #333;
        }
        
        .header-section {
            margin-bottom: 20px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
        }
        
        .header-info {
            margin-bottom: 15px;
        }
        
        .info-row {
            display: inline-block;
            width: 50%;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #000;
            display: inline-block;
            width: 40%;
        }
        
        .info-value {
            display: inline-block;
            width: 60%;
        }
        
        .summary-section {
            margin-bottom: 20px;
            border-collapse: collapse;
            background-color: #f5f5f5;
        }
        
        .summary-title {
            font-size: 13px;
            font-weight: bold;
            color: #000;
            margin-bottom: 8px;
            padding: 8px;
            background-color: #000;
            color: white;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th {
            background-color: #000;
            color: white;
            font-weight: bold;
            padding: 10px 8px;
            text-align: left;
            border: 1px solid #000;
            font-size: 11px;
        }
        
        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        
        tr:nth-child(even) {
            background-color: #fff;
        }
        
        .summary-row {
            background-color: #f5f5f5;
        }
        
        .summary-row td {
            border: 1px solid #ddd;
            padding: 10px 8px;
        }
        
        .summary-row td:first-child {
            font-weight: bold;
            width: 40%;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #999;
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="report-title">📋 TIME LOGS REPORT</div>
    
    <div class="header-section">
        <div class="header-info">
            <div class="info-row">
                <span class="info-label">Employee Name:</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
        </div>
        <div class="header-info">
            <div class="info-row">
                @if($company)
                <span class="info-label">Organization:</span>
                <span class="info-value">{{ $company->name }}</span>
                @endif
            </div>
            <div class="info-row">
                <span class="info-label">Generated:</span>
                <span class="info-value">{{ now()->format('M d, Y H:i') }}</span>
            </div>
        </div>
    </div>

    @if($logs->count() > 0)
        <div class="summary-section">
            <div class="summary-title">SUMMARY</div>
            <table>
                <tr class="summary-row">
                    <td>Total Hours Logged</td>
                    <td class="text-right"><strong>{{ number_format($totalHours, 2) }}</strong> hours</td>
                </tr>
                <tr class="summary-row">
                    <td>Rendered Hours</td>
                    <td class="text-right"><strong>{{ number_format($totalRenderedHours, 2) }}</strong> hours</td>
                </tr>
                <tr class="summary-row">
                    <td>Total Days Worked</td>
                    <td class="text-right"><strong>{{ $logs->count() }}</strong> sessions</td>
                </tr>
                <tr class="summary-row">
                    <td>Average Hours/Day</td>
                    <td class="text-right">
                        <strong>
                            @php
                                $avgHours = $logs->count() > 0 ? $totalRenderedHours / $logs->count() : 0;
                            @endphp
                            {{ number_format($avgHours, 2) }}
                        </strong> hours
                    </td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 10%;">Time In</th>
                    <th style="width: 10%;">Time Out</th>
                    <th style="width: 12%;">Total Hours</th>
                    <th style="width: 12%;">Rendered Hours</th>
                    <th style="width: 40%;">Notes</th>
                    <th style="width: 8%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                @php
                    $renderedHours = $log->total_hours > 1 ? $log->total_hours - 1 : $log->total_hours;
                @endphp
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($log->time_in)->format('H:i') }}</td>
                    <td class="text-center">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : '—' }}</td>
                    <td class="text-right">{{ number_format($log->total_hours, 2) }}</td>
                    <td class="text-right">{{ number_format($renderedHours, 2) }}</td>
                    <td>{{ $log->notes ?: '—' }}</td>
                    <td class="text-center"><strong>{{ ucfirst($log->status) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 30px; color: #999;">
            <em>No time logs recorded in the system.</em>
        </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by the Time Tracker System.</p>
    </div>
</body>
</html>
