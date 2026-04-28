<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $company->name }} - Analytics Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #000;
            background: #fff;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px;
        }

        .header {
            background: white;
            color: black;
            padding: 30px;
            border: 2px solid #000;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .header-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .header-row {
            display: table-row;
        }

        .header-col {
            display: table-cell;
            padding: 12px;
            border: 1px solid #e5e5e5;
            width: 50%;
        }

        .header-label {
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            margin-bottom: 4px;
        }

        .header-value {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        .summary-section {
            margin-bottom: 30px;
        }

        .summary-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px;
            background: #000;
            color: white;
            margin-bottom: 0;
        }

        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            padding: 16px;
            border: 1px solid #e5e5e5;
            width: 25%;
            text-align: center;
        }

        .summary-cell-label {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            margin-bottom: 8px;
        }

        .summary-cell-value {
            font-size: 24px;
            font-weight: 900;
            color: #000;
            font-variant-numeric: tabular-nums;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table thead {
            background: #000;
            color: white;
        }

        .data-table th {
            padding: 12px;
            text-align: left;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000;
        }

        .data-table td {
            padding: 12px;
            border: 1px solid #e5e5e5;
            font-size: 11px;
        }

        .data-table tbody tr:nth-child(odd) {
            background: #fafafa;
        }

        .data-table tbody tr:nth-child(even) {
            background: #fff;
        }

        .numeric {
            font-variant-numeric: tabular-nums;
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #000;
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .batch-badge {
            display: inline-block;
            background: #000;
            color: white;
            padding: 4px 8px;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                <span class="batch-badge">{{ $company->batch }}</span>
                <h1>{{ $company->name }}</h1>
            </div>
            
            <div class="header-grid">
                <div class="header-row">
                    <div class="header-col">
                        <div class="header-label">Organization</div>
                        <div class="header-value">{{ $company->name }}</div>
                    </div>
                    <div class="header-col">
                        <div class="header-label">Batch</div>
                        <div class="header-value">{{ $company->batch }}</div>
                    </div>
                </div>
                <div class="header-row">
                    <div class="header-col">
                        <div class="header-label">Total Students</div>
                        <div class="header-value">{{ $students->count() }}</div>
                    </div>
                    <div class="header-col">
                        <div class="header-label">Generated</div>
                        <div class="header-value">{{ $generatedAt->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        @php
            $totalCompanyHours = $students->sum('rendered_hours');
            $totalDaysWorked = $students->sum('days_worked');
            $avgHoursPerStudent = $students->count() > 0 ? $students->sum('average_hours') / $students->count() : 0;
        @endphp
        
        <div class="summary-section">
            <div class="summary-title">Room Summary</div>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">
                        <div class="summary-cell-label">Total Hours Rendered</div>
                        <div class="summary-cell-value">{{ number_format($totalCompanyHours, 1) }}h</div>
                    </div>
                    <div class="summary-cell">
                        <div class="summary-cell-label">Total Sessions</div>
                        <div class="summary-cell-value">{{ $totalDaysWorked }}</div>
                    </div>
                    <div class="summary-cell">
                        <div class="summary-cell-label">Avg Hours/Student</div>
                        <div class="summary-cell-value">{{ number_format($avgHoursPerStudent, 1) }}h</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="summary-section">
            <div class="summary-title">Student Performance</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th class="numeric">Sessions</th>
                        <th class="numeric">Total Hours</th>
                        <th class="numeric">Rendered Hours</th>
                        <th class="numeric">Average/Day</th>
                        <th class="numeric">Progress %</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $progress = min(100, ($student['rendered_hours'] / 486) * 100);
                        @endphp
                        <tr>
                            <td style="font-weight: 600;">{{ $student['name'] }}</td>
                            <td>{{ $student['email'] }}</td>
                            <td class="numeric">{{ $student['days_worked'] }}</td>
                            <td class="numeric">{{ number_format($student['total_hours'], 2) }}h</td>
                            <td class="numeric">{{ number_format($student['rendered_hours'], 2) }}h</td>
                            <td class="numeric">{{ number_format($student['average_hours'], 2) }}h</td>
                            <td class="numeric">{{ number_format($progress, 1) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px; color: #999;">No students enrolled in this room</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Analytics Report for {{ $company->name }} | Generated on {{ $generatedAt->format('F d, Y \a\t H:i:s') }}</p>
            <p style="margin-top: 8px;">This document contains confidential information related to OJT performance metrics.</p>
        </div>
    </div>
</body>
</html>
