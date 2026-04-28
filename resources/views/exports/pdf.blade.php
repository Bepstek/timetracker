<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Time Logs Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            background: white;
            color: black;
            padding: 30px;
            margin-bottom: 30px;
            border-bottom: 4px solid #000;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: black;
        }

        .header-info {
            display: table;
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .header-row {
            display: table-row;
        }

        .header-col {
            display: table-cell;
            width: 50%;
            padding: 10px 15px;
            font-size: 13px;
            color: black;
            border-right: 1px solid #ddd;
        }

        .header-col:nth-child(2n) {
            border-right: none;
        }

        .header-col strong {
            display: block;
            color: black;
            font-size: 13px;
            font-weight: normal;
            text-transform: none;
            margin-bottom: 0px;
            letter-spacing: 0px;
        }

        .summary-section {
            background-color: #f5f5f5;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #ddd;
        }

        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 0;
        }

        .summary-cell {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border-right: 1px solid #ddd;
        }

        .summary-cell:last-child {
            border-right: none;
        }

        .summary-cell .value {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin: 5px 0;
        }

        .summary-cell .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-wrapper {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        thead {
            background-color: #000;
            color: white;
        }

        th {
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000;
        }

        td {
            padding: 10px 12px;
            font-size: 11px;
            border-bottom: 1px solid #ddd;
            border-left: 1px solid #f0f0f0;
            border-right: 1px solid #f0f0f0;
        }

        tbody tr:nth-child(odd) {
            background-color: #fafafa;
        }

        tbody tr:nth-child(even) {
            background-color: #fff;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        td {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .status-completed {
            color: #27ae60;
            font-weight: bold;
        }

        .status-active {
            color: #e74c3c;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            font-size: 10px;
            color: #999;
            text-align: center;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }

        .mono {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="font-weight: bold;">Time Logs Report</h1>
        <div class="header-info">
            <div class="header-row">
                <div class="header-col"><strong>{{ $user->name }}</strong></div>
                <div class="header-col"><strong>{{ $user->email }}</strong></div>
            </div>
            <div class="header-row">
                <div class="header-col">
                    @if($company)
                    <strong>{{ $company->name }}</strong>
                    @else
                    <strong>—</strong>
                    @endif
                </div>
                <div class="header-col"><strong>{{ now()->format('M d, Y \a\t H:i') }}</strong></div>
            </div>
        </div>
    </div>

    @if($logs->count() > 0)
        <div class="summary-section">
            <div class="summary-grid">
                <div class="summary-cell">
                    <div class="label">Total Hours Logged</div>
                    <div class="value">{{ number_format($logs->sum('total_hours'), 2) }}</div>
                    <div class="label">hours</div>
                </div>
                <div class="summary-cell">
                    <div class="label">Rendered Hours</div>
                    <div class="value">{{ number_format($totalRenderedHours, 2) }}</div>
                    <div class="label">hours</div>
                </div>
                <div class="summary-cell">
                    <div class="label">Total Days Worked</div>
                    <div class="value">{{ $logs->count() }}</div>
                    <div class="label">sessions</div>
                </div>
                <div class="summary-cell">
                    <div class="label">Average Hours/Day</div>
                    <div class="value">
                        @php
                            $avgHours = $logs->count() > 0 ? $totalRenderedHours / $logs->count() : 0;
                        @endphp
                        {{ number_format($avgHours, 2) }}
                    </div>
                    <div class="label">hours</div>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">Date</th>
                        <th style="width: 10%;">Time In</th>
                        <th style="width: 10%;">Time Out</th>
                        <th style="width: 12%;">Total Hours</th>
                        <th style="width: 12%;">Rendered Hours</th>
                        <th style="width: 35%;">Notes</th>
                        <th style="width: 9%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    @php
                        $renderedHours = $log->total_hours > 1 ? $log->total_hours - 1 : $log->total_hours;
                    @endphp
                    <tr>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                        </td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($log->time_in)->format('H:i') }}
                        </td>
                        <td class="text-center">
                            {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : '—' }}
                        </td>
                        <td class="text-center mono">
                            {{ number_format($log->total_hours, 2) }}
                        </td>
                        <td class="text-center mono">
                            {{ number_format($renderedHours, 2) }}
                        </td>
                        <td>
                            {{ $log->notes ?: '—' }}
                        </td>
                        <td class="text-center">
                            <span class="@if($log->status === 'completed') status-completed @else status-active @endif">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="no-data">
            <p>No time logs recorded in the system.</p>
        </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by the Time Tracker System.</p>
        <p style="margin-top: 5px;">For more information, please contact your administrator.</p>
    </div>
</body>
</html>
