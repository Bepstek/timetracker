@extends('app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Timer Card (Span 2 cols on large screens) -->
    <div class="lg:col-span-2">
        <div class="bw-panel h-full p-8 relative">
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
                        const startTime = new Date("{{ \Carbon\Carbon::parse($activeLog->date->format('Y-m-d') . ' ' . $activeLog->time_in)->toIso8601String() }}").getTime();
                        
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
<div class="mt-12">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-black uppercase tracking-wide">Yearly Activity Chart</h3>
    </div>
    
    <div class="bw-panel p-6 bg-white">
        <div class="relative w-full h-96">
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
