@extends('app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Timer Card (Span 2 cols on large screens) -->
    <div class="lg:col-span-2 space-y-6">
        @if($user->company)
            <div class="flex items-center justify-between bg-black text-white px-6 py-4 border-2 border-black">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/10">
                        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] leading-none mb-1">Assigned OJT Room</p>
                        <h4 class="text-xs font-black uppercase tracking-widest">{{ $user->company->name }} <span class="text-gray-500 ml-2">— {{ $user->company->batch }}</span></h4>
                    </div>
                </div>
            </div>
        @else
            <div class="flex items-center gap-4 bg-gray-50 border-2 border-dashed border-gray-200 p-6">
                <div class="p-3 bg-white border border-gray-200">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.268 15c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <h4 class="text-xs font-black text-black uppercase tracking-widest">Awaiting Assignment</h4>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Contact your administrator to join an OJT Room</p>
                </div>
            </div>
        @endif

        <!-- @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-600 p-4 flex items-center gap-3">
                <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest">{{ $errors->first() }}</p>
            </div>
        @endif -->

        <div class="bw-panel p-8 relative">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-black uppercase tracking-wide flex items-center">
                    <div class="h-2 w-2 bg-black mr-3"></div>
                    Current Session
                </h3>
                @if($activeLog)
                    <div class="flex items-center">
                        <span class="relative flex h-3 w-3 mr-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-black opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-black"></span>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-widest text-black">Active</span>
                    </div>
                @else
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Inactive</span>
                @endif
            </div>

            <div class="text-center py-6">
                @if($activeLog)
                    <div class="text-4xl sm:text-6xl md:text-8xl font-black text-black tracking-tighter mb-4 tabular-nums leading-none" id="timer">00:00:00</div>
                    <p class="text-gray-500 mb-10 text-sm uppercase tracking-widest">Started at {{ \Carbon\Carbon::parse($activeLog->time_in)->format('H:i') }}</p>
                    
                    <form action="{{ route('clock-out', $activeLog) }}" method="POST" class="max-w-xl mx-auto">
                        @csrf
                        @method('PUT')
                        <div class="mb-6">
                             <input name="notes" placeholder="Enter session notes..." class="bw-input block w-full text-sm p-4 text-black placeholder-gray-400">
                        </div>
                        <button type="submit" class="bw-button w-full py-4 text-base font-bold uppercase tracking-widest border border-black">
                            Stop Timer
                        </button>
                    </form>

                    <script>
                        @php
                            $timeIn = $activeLog->time_in;
                            if ($timeIn instanceof \Carbon\Carbon) {
                                $timeIn = $timeIn->format('H:i:s');
                            } elseif (strlen($timeIn) > 8) {
                                $timeIn = date('H:i:s', strtotime($timeIn));
                            }
                            $isoStart = \Carbon\Carbon::parse($activeLog->date->format('Y-m-d') . ' ' . $timeIn)->toIso8601String();
                        @endphp
                        const startTime = new Date("{{ $isoStart }}").getTime();
                        
                        function updateTimer() {
                            const now = new Date().getTime();
                            const distance = now - startTime;
                            
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            
                            document.getElementById("timer").innerHTML = 
                                (hours < 10 ? "0" + hours : hours) + ":" + 
                                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                                (seconds < 10 ? "0" + seconds : seconds);
                        }
                        
                        setInterval(updateTimer, 1000);
                        updateTimer();
                    </script>
                @else
                    <div class="py-12">
                        <div class="text-gray-400 mb-8 text-xl font-light">Ready to start tracking?</div>
                        <form action="{{ route('clock-in') }}" method="POST" class="max-w-sm mx-auto">
                            @csrf
                            <button type="submit" class="bw-button w-full py-4 text-lg font-bold uppercase tracking-widest border border-black">
                                Start Timer
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="lg:col-span-1">
        <div class="bw-panel h-full p-8 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-black uppercase tracking-wide mb-6">Total Summary</h3>
                <div class="flex items-baseline mb-2">
                    <span class="text-7xl font-black text-black tracking-tighter">{{ number_format($totalSummary, 2) }}</span>
                    <span class="ml-2 text-xl font-medium text-gray-500">HRS</span>
                </div>
                <div class="w-full bg-gray-100 h-1 mb-6">
                    <!-- Progress bar removed as it doesn't make sense for "Total" vs daily goal -->
                    <div class="bg-black h-1 transition-all duration-500" style="width: 100%"></div>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500 uppercase tracking-wider">Total Sessions</span>
                    <span class="text-lg font-bold text-black">{{ $user->timeLogs()->count() }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500 uppercase tracking-wider">Required</span>
                    <span class="text-lg font-bold text-black">486.00 HRS</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-500 uppercase tracking-wider">Daily Avg</span>
                    <span class="text-lg font-bold text-black">{{ number_format($averageHoursPerDay, 2) ?: '0.00' }} HRS</span>
                </div>
                <div class="flex justify-between items-center border-t border-gray-100 pt-3 mt-1">
                    <span class="text-sm text-black font-bold uppercase tracking-widest">Est. Days Left</span>
                    <span class="text-2xl font-black text-black tracking-tighter">{{ $daysLeft }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Recent Logs / Chart -->
<div class="mt-12 grid grid-cols-1 xl:grid-cols-2 gap-8">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-black uppercase tracking-wide">Recent History</h3>
            <button onclick="window.location.href='{{ route('logs') }}'" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-black transition-colors">View All Logs</button>
        </div>

        <div class="bw-panel overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Date</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Time</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Hours</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Rendered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-black">{{ $log->date->format('M d, Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-500">
                                    {{ \Carbon\Carbon::parse($log->time_in)->format('H:i') }} - 
                                    {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : '--:--' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $hours = floor($log->total_hours);
                                    $minutes = round(($log->total_hours - $hours) * 60);
                                @endphp
                                <span class="text-xs font-black text-black">{{ $hours }}h {{ $minutes }}m</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $renderedHours = $log->total_hours > 1 ? $log->total_hours - 1 : $log->total_hours;
                                    $hours = floor($renderedHours);
                                    $minutes = round(($renderedHours - $hours) * 60);
                                @endphp
                                <span class="text-xs font-black text-black">{{ $hours }}h {{ $minutes }}m</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic text-xs uppercase tracking-widest">No logs recorded yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Add Manual Log Shortcut -->
        <div x-data="{ open: false }" class="mt-4">
            @if($user->company_id)
                <button @click="open = true" class="w-full py-4 border-2 border-dashed border-gray-200 text-[10px] font-black uppercase tracking-[0.3em] text-gray-400 hover:border-black hover:text-black transition-all">
                    + Add Manual Log Entry
                </button>
            @else
                <button disabled class="w-full py-4 border-2 border-dashed border-gray-100 text-[10px] font-black uppercase tracking-[0.3em] text-gray-300 cursor-not-allowed">
                    + Manual Entry Locked (No Room Assigned)
                </button>
            @endif

            <!-- Manual Log Modal -->
            <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
                <div @click.away="open = false" class="bw-panel w-full max-w-md p-8 bg-white">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-black text-black uppercase tracking-tight">Manual Entry</h3>
                        <button @click="open = false" class="text-gray-400 hover:text-black">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form action="{{ route('logs.manual') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Date</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="bw-input w-full p-4 text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Time In</label>
                                <input type="time" name="time_in" required class="bw-input w-full p-4 text-sm">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Time Out</label>
                                <input type="time" name="time_out" required class="bw-input w-full p-4 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Notes</label>
                            <textarea name="notes" rows="3" class="bw-input w-full p-4 text-sm" placeholder="What did you work on?"></textarea>
                        </div>
                        <button type="submit" class="bw-button w-full py-4 font-bold uppercase tracking-widest">
                            Save Log Entry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-black uppercase tracking-wide">Yearly Activity</h3>
        </div>
        
        <div class="bw-panel p-6 bg-white min-h-[400px]">
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('weeklyChart').getContext('2d');
        
        // Data from PHP
        const labels = @json($chartLabels);
        const data = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Hours Worked',
                    data: data,
                    backgroundColor: 'rgba(0,0,0,0.05)',
                    borderColor: '#000000',
                    borderWidth: 1.5,
                    fill: true,
                    tension: 0,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#000000',
                    pointBorderWidth: 1.5,
                    pointRadius: 0, // Controlled by global option but reset here to ensure
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#9ca3af',
                            font: { family: 'Inter', weight: '500' }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#000000',
                            font: { family: 'Inter', weight: '600' },
                            maxTicksLimit: 12, // Show roughly one label per month
                            maxRotation: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#000000',
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 14 },
                        padding: 10,
                        cornerRadius: 0,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' Hours';
                            }
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 0, // Hide points by default for cleaner look on large datasets
                        hoverRadius: 6
                    }
                },
                animation: {
                    duration: 0
                }
            }
        });
    });
</script>
@endsection
