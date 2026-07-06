@extends('layouts.khas')
@section('title', 'Reports')

@section('tabs')
    <a href="{{ route('teacher.dashboard') }}" class="khas-tab">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>
    <a href="{{ route('teacher.students') }}" class="khas-tab">
        <i class="fa-solid fa-users"></i> Students
    </a>
    <a href="{{ route('teacher.rpi') }}" class="khas-tab">
        <i class="fa-solid fa-file-lines"></i> RPI / IEP
    </a>
    <a href="{{ route('teacher.messages') }}" class="khas-tab">
        <i class="fa-solid fa-comments"></i> Messages
        @if($unreadMessages > 0)
            <span class="notif-dot">{{ $unreadMessages }}</span>
        @endif
    </a>
    <a href="{{ route('teacher.reports') }}" class="khas-tab active">
        <i class="fa-solid fa-chart-bar"></i> Reports
    </a>
@endsection

@section('content')
<div class="khas-page">

    {{-- Header --}}
    <div class="khas-page-header">
        <div>
            <h5 style="font-weight:600;margin-bottom:2px">Behaviour Report</h5>
            <p style="font-size:12.5px;color:var(--khas-muted);margin:0">
                {{ $months[$month] }} {{ $year }}
                @if($class)
                    &nbsp;&middot;&nbsp; {{ $class->class_name }}
                @endif
            </p>
        </div>

        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            {{-- Month/year filter --}}
            <form method="GET" style="display:flex;gap:8px;align-items:center">
                <select name="month" class="khas-select"
                        style="width:130px;margin-bottom:0">
                    @foreach($months as $m => $name)
                        @if($m > 0)
                        <option value="{{ $m }}"
                                {{ $m == $month ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                        @endif
                    @endforeach
                </select>
                <select name="year" class="khas-select"
                        style="width:90px;margin-bottom:0">
                    @for($y = now()->year; $y >= 2024; $y--)
                    <option value="{{ $y }}"
                            {{ $y == $year ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                    @endfor
                </select>
                <button type="submit" class="khas-btn khas-btn-primary"
                        style="width:auto">
                    <i class="fa-solid fa-eye"></i> View
                </button>
            </form>

            {{-- Export PDF button --}}
            <a href="{{ route('teacher.reports.export', ['month' => $month, 'year' => $year]) }}"
               class="khas-btn"
               style="width:auto;background:#fff;color:var(--khas-red);
                      border:1.5px solid var(--khas-red);font-weight:600;
                      text-decoration:none;display:inline-flex;align-items:center;
                      gap:7px;padding:9px 16px;border-radius:8px;font-size:13px;
                      transition:all 0.18s"
               onmouseover="this.style.background='#FDEDEC'"
               onmouseout="this.style.background='#fff'">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- Summary stat cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);
                gap:10px;margin-bottom:20px">
        <div class="stat-card">
            <div style="font-size:18px;color:var(--khas-blue);margin-bottom:6px">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div class="stat-num" style="color:var(--khas-blue)">{{ $totalLogs }}</div>
            <div class="stat-label">Total logs this month</div>
        </div>
        <div class="stat-card">
            <div style="font-size:18px;color:var(--khas-green);margin-bottom:6px">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-num" style="color:var(--khas-green)">
                @php
                    $resolved = $reportData->sum('resolved_count');
                    $rate     = $totalLogs > 0
                              ? round(($resolved / $totalLogs) * 100) : 0;
                @endphp
                {{ $rate }}%
            </div>
            <div class="stat-label">Resolution rate</div>
        </div>
        <div class="stat-card">
            <div style="font-size:18px;color:var(--khas-amber);margin-bottom:6px">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="stat-num" style="color:var(--khas-amber)">
                {{ $reportData->where('total_logs', '>', 0)->count() }}
            </div>
            <div class="stat-label">Students with incidents</div>
        </div>
    </div>

    {{-- Charts row --}}
    @if($totalLogs > 0)
    <div style="display:grid;grid-template-columns:1.6fr 1fr;
                gap:14px;margin-bottom:20px">

        {{-- Bar chart: logs per student --}}
        <div class="khas-card">
            <p style="font-size:13px;font-weight:600;margin-bottom:14px">
                <i class="fa-solid fa-chart-bar"
                   style="color:var(--khas-blue)"></i>
                &nbsp;Logs per Student
            </p>
            <div style="position:relative;height:220px">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        {{-- Doughnut chart: behaviour type breakdown --}}
        <div class="khas-card">
            <p style="font-size:13px;font-weight:600;margin-bottom:14px">
                <i class="fa-solid fa-chart-pie"
                   style="color:var(--khas-blue)"></i>
                &nbsp;Behaviour Types
            </p>
            <div style="position:relative;height:220px">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>

    </div>
    @endif

    {{-- Per-student table --}}
    <div class="khas-card">
        <p style="font-size:13.5px;font-weight:600;margin-bottom:14px">
            Per-Student Summary
        </p>
        @if($reportData->isEmpty())
        <p style="font-size:13px;color:var(--khas-muted)">
            No data for this period.
        </p>
        @else
        <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:var(--khas-bg)">
                    <th style="text-align:left;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">STUDENT</th>
                    <th style="text-align:center;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">LOGS</th>
                    <th style="text-align:center;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">AVG SEVERITY</th>
                    <th style="text-align:center;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">RESOLVED</th>
                    <th style="text-align:left;padding:10px 14px;font-weight:600;
                               font-size:12px;color:var(--khas-muted)">BEHAVIOUR TYPES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                <tr style="border-top:1px solid var(--khas-border)">
                    <td style="padding:11px 14px">
                        <p style="font-weight:600;margin:0 0 2px">
                            {{ $row['student']->name }}
                        </p>
                        <span class="badge-autism">Autism</span>
                    </td>
                    <td style="text-align:center;padding:11px 14px;font-weight:700;
                               color:{{ $row['total_logs'] > 0
                                       ? 'var(--khas-red)'
                                       : 'var(--khas-muted)' }}">
                        {{ $row['total_logs'] }}
                    </td>
                    <td style="text-align:center;padding:11px 14px">
                        {{ $row['total_logs'] > 0
                            ? $row['avg_severity']
                            : '—' }}
                    </td>
                    <td style="text-align:center;padding:11px 14px">
                        @if($row['total_logs'] > 0)
                            <span style="color:var(--khas-green);font-weight:600">
                                {{ $row['resolved_count'] }}/{{ $row['total_logs'] }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding:11px 14px;font-size:12px;
                               color:var(--khas-muted)">
                        {{ $row['types'] ?: '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
@if($totalLogs > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const blue   = '#2558A0';
const green  = '#27AE6D';
const amber  = '#E8991C';
const red    = '#C94040';
const purple = '#5B4ECC';
const teal   = '#74C8D8';

const paletteColors = [blue, red, amber, green, purple, teal,
    '#E67E22','#8E44AD','#16A085','#2C3E50'];

// ── Bar chart ─────────────────────────────────────────────────
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Behaviour Logs',
            data:  {!! json_encode($chartData) !!},
            backgroundColor: {!! json_encode(
                array_map(fn($i) => ['#2558A0','#C94040','#E8991C','#27AE6D',
                                     '#5B4ECC','#74C8D8'][$i % 6],
                          array_keys($chartLabels))
            ) !!},
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.parsed.y} log${ctx.parsed.y !== 1 ? 's' : ''}`
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    font: { family: 'Poppins', size: 11 },
                    color: '#6B7A8D'
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: { family: 'Poppins', size: 11 },
                    color: '#6B7A8D'
                },
                grid: { color: '#F1F5F9' }
            }
        }
    }
});

// ── Doughnut chart ────────────────────────────────────────────
const dCtx = document.getElementById('doughnutChart').getContext('2d');
new Chart(dCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($typeLabels) !!},
        datasets: [{
            data:            {!! json_encode($typeCounts) !!},
            backgroundColor: paletteColors.slice(0, {!! count($typeLabels) !!}),
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: { family: 'Poppins', size: 11 },
                    color: '#6B7A8D',
                    padding: 12,
                    usePointStyle: true,
                    pointStyleWidth: 8,
                }
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed} incidents`
                }
            }
        },
        cutout: '60%',
    }
});
</script>
@endif
@endpush
