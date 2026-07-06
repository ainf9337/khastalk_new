<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #1A2332;
            padding: 32px;
            line-height: 1.5;
        }

        /* ── Header ─────────────────────────────────── */
        .pdf-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1E4A87;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .pdf-system-name {
            font-size: 22px;
            font-weight: 700;
            color: #1E4A87;
            letter-spacing: -0.5px;
        }

        .pdf-system-sub {
            font-size: 10px;
            color: #6B7A8D;
            margin-top: 2px;
        }

        .pdf-meta {
            text-align: right;
            font-size: 11px;
            color: #6B7A8D;
        }

        .pdf-meta strong {
            color: #1A2332;
            display: block;
            font-size: 13px;
            margin-bottom: 2px;
        }

        /* ── Title ──────────────────────────────────── */
        .pdf-title {
            font-size: 16px;
            font-weight: 700;
            color: #1A2332;
            margin-bottom: 4px;
        }

        .pdf-subtitle {
            font-size: 11px;
            color: #6B7A8D;
            margin-bottom: 20px;
        }

        /* ── Summary boxes ──────────────────────────── */
        .summary-row {
            display: flex;
            gap: 12px;
            margin-bottom: 22px;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #E8EDF4;
            border-radius: 8px;
            padding: 12px 14px;
            text-align: center;
        }

        .summary-box .s-num {
            font-size: 24px;
            font-weight: 700;
            color: #1E4A87;
            display: block;
        }

        .summary-box .s-label {
            font-size: 10px;
            color: #6B7A8D;
            margin-top: 2px;
        }

        /* ── Table ──────────────────────────────────── */
        .pdf-section-title {
            font-size: 12px;
            font-weight: 700;
            color: #1E4A87;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #E8EDF4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-bottom: 22px;
        }

        thead tr {
            background: #EBF1FA;
        }

        thead th {
            text-align: left;
            padding: 8px 10px;
            font-weight: 700;
            color: #1E4A87;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        tbody tr {
            border-bottom: 1px solid #E8EDF4;
        }

        tbody tr:nth-child(even) {
            background: #F8FAFC;
        }

        tbody td {
            padding: 9px 10px;
            color: #1A2332;
            vertical-align: top;
        }

        .td-center { text-align: center; }
        .td-muted  { color: #6B7A8D; }

        .badge {
            background: #EDE9FE;
            color: #5B4ECC;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            display: inline-block;
        }

        .resolved {
            color: #27AE6D;
            font-weight: 700;
        }

        .incidents {
            color: #C94040;
            font-weight: 700;
        }

        /* ── Footer ─────────────────────────────────── */
        .pdf-footer {
            border-top: 1px solid #E8EDF4;
            padding-top: 12px;
            margin-top: 10px;
            font-size: 10px;
            color: #9CA3AF;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="pdf-header">
        <div>
            <div class="pdf-system-name">KHAS-Talk</div>
            <div class="pdf-system-sub">
                Sistem Komunikasi Ibu Bapa – Guru
            </div>
        </div>
        <div class="pdf-meta">
            <strong>Behaviour Report</strong>
            Generated: {{ now()->format('d M Y, g:i a') }}<br>
            Teacher: {{ $teacher->name }}<br>
            Class: {{ $class?->class_name ?? 'N/A' }}
        </div>
    </div>

    {{-- Title --}}
    <div class="pdf-title">
        Monthly Behaviour Report
    </div>
    <div class="pdf-subtitle">
        Period: {{ $months[$month] }} {{ $year }}
        &nbsp;|&nbsp; Class: {{ $class?->class_name ?? 'N/A' }}
        &nbsp;|&nbsp; Academic Year: {{ $class?->academic_year ?? now()->year }}
    </div>

    {{-- Summary --}}
    <div class="summary-row">
        <div class="summary-box">
            <span class="s-num">{{ $totalLogs }}</span>
            <span class="s-label">Total Incidents</span>
        </div>
        <div class="summary-box">
            <span class="s-num" style="color:#27AE6D">{{ $resolutionRate }}%</span>
            <span class="s-label">Resolution Rate</span>
        </div>
        <div class="summary-box">
            <span class="s-num" style="color:#E8991C">
                {{ $reportData->where('total_logs', '>', 0)->count() }}
            </span>
            <span class="s-label">Students with Incidents</span>
        </div>
        <div class="summary-box">
            <span class="s-num" style="color:#6B7A8D">
                {{ $reportData->count() }}
            </span>
            <span class="s-label">Total Students</span>
        </div>
    </div>

    {{-- Per-student table --}}
    <div class="pdf-section-title">Per-Student Incident Summary</div>
    <table>
        <thead>
            <tr>
                <th style="width:30%">Student</th>
                <th class="td-center" style="width:10%">Logs</th>
                <th class="td-center" style="width:12%">Avg Severity</th>
                <th class="td-center" style="width:14%">Resolved</th>
                <th style="width:34%">Behaviour Types</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
            <tr>
                <td>
                    <strong>{{ $row['student']->name }}</strong><br>
                    <span class="badge">Autism</span>
                </td>
                <td class="td-center">
                    @if($row['total_logs'] > 0)
                        <span class="incidents">{{ $row['total_logs'] }}</span>
                    @else
                        <span class="td-muted">0</span>
                    @endif
                </td>
                <td class="td-center td-muted">
                    {{ $row['total_logs'] > 0 ? $row['avg_severity'] : '—' }}
                </td>
                <td class="td-center">
                    @if($row['total_logs'] > 0)
                        <span class="resolved">
                            {{ $row['resolved_count'] }}/{{ $row['total_logs'] }}
                        </span>
                    @else
                        <span class="td-muted">—</span>
                    @endif
                </td>
                <td class="td-muted">
                    {{ $row['types'] ?: 'No incidents recorded' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Notes section --}}
    <div class="pdf-section-title">Teacher's Notes</div>
    <table>
        <thead>
            <tr>
                <th style="width:30%">Student</th>
                <th>Observations & Recommendations</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData->where('total_logs', '>', 0) as $row)
            <tr>
                <td>{{ $row['student']->name }}</td>
                <td class="td-muted" style="font-style:italic;color:#9CA3AF">
                    _______________________________________________
                </td>
            </tr>
            @endforeach
            @if($reportData->where('total_logs', '>', 0)->isEmpty())
            <tr>
                <td colspan="2" class="td-muted" style="text-align:center;padding:16px">
                    No incidents recorded this month. Alhamdulillah!
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="pdf-footer">
        <span>
            KHAS-Talk &mdash; Parent-Teacher Communication System for Autism
        </span>
        <span>
            Developed by Nur Ain Farhana Binti Ahmad Saifful
            &nbsp;|&nbsp; {{ now()->format('d M Y') }}
        </span>
    </div>

</body>
</html>
