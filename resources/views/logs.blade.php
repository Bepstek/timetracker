@extends('app')

@section('content')
@if ($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            </div>
        </div>
    </div>
@endif

<div class="bw-panel p-0 overflow-hidden">
    <div class="px-6 py-6 border-b border-black flex justify-between items-center bg-white">
        <h3 class="text-xl font-bold text-black uppercase tracking-wide">Time Logs History</h3>
        <div class="flex space-x-2">
            @if(auth()->user()->company_id)
                <button onclick="document.getElementById('manualLogModal').classList.remove('hidden')" class="bw-button px-5 py-2 text-xs font-bold uppercase tracking-widest border border-black flex items-center">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Manual Log
                </button>
            @else
                <button disabled title="Assign a company first to add logs" class="bw-button px-5 py-2 text-xs font-bold uppercase tracking-widest border border-gray-200 text-gray-300 flex items-center cursor-not-allowed">
                    <svg class="h-4 w-4 mr-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Manual Log (Locked)
                </button>
            @endif
            <div class="relative group">
                <button class="bw-button px-5 py-2 text-xs font-bold uppercase tracking-widest border border-black flex items-center bg-black text-white group-hover:bg-gray-900">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export <span class="ml-2">▼</span>
                </button>
                <div class="absolute right-0 mt-0 w-40 bg-white border border-black shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-10">
                    <a href="{{ route('logs.export', ['type' => 'excel']) }}" class="block px-4 py-3 text-xs font-bold uppercase tracking-widest text-black hover:bg-gray-100 border-b border-gray-200">
                        <svg class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Export Excel
                    </a>
                    <a href="{{ route('logs.export', ['type' => 'pdf']) }}" class="block px-4 py-3 text-xs font-bold uppercase tracking-widest text-black hover:bg-gray-100">
                        <svg class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">In</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Out</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Total</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Rendered</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Notes</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-5 text-sm font-medium text-black">
                        {{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-5 text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($log->time_in)->format('H:i') }}
                    </td>
                    <td class="px-6 py-5 text-sm text-gray-500">
                        {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : '—' }}
                    </td>
                    <td class="px-6 py-5 text-sm font-mono font-bold text-black">
                        @php
                            $hours = floor($log->total_hours);
                            $minutes = round(($log->total_hours - $hours) * 60);
                        @endphp
                        {{ $hours }}h {{ $minutes }}m
                    </td>
                    <td class="px-6 py-5 text-sm font-mono font-bold text-black">
                        @php
                            $renderedHours = $log->total_hours > 1 ? $log->total_hours - 1 : $log->total_hours;
                            $hours = floor($renderedHours);
                            $minutes = round(($renderedHours - $hours) * 60);
                        @endphp
                        {{ $hours }}h {{ $minutes }}m
                    </td>
                    <td class="px-6 py-5 text-sm text-gray-500 max-w-xs truncate">
                        {{ $log->notes ?: '—' }}
                    </td>
                    <td class="px-6 py-5">
                        <span class="inline-flex text-xs leading-5 font-bold uppercase tracking-wide {{ $log->status === 'active' ? 'text-black' : 'text-gray-400' }}">
                            {{ $log->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No logs recorded in the system.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
        
    <div class="px-6 py-4 border-t border-black bg-white">
        {{ $logs->links() }}
    </div>
</div>

<!-- Manual Log Modal -->
<div id="manualLogModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white border-2 border-black w-full max-w-md p-8 relative">
        <button onclick="document.getElementById('manualLogModal').classList.add('hidden')" class="absolute top-4 right-4 text-black hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <h3 class="text-2xl font-black uppercase tracking-tight mb-6">Add Manual Log</h3>
        
        <form action="{{ route('logs.manual') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-black mb-1">Date</label>
                    <input type="date" name="date" required max="{{ date('Y-m-d') }}" class="bw-input w-full p-3 border border-black focus:ring-0">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-black mb-1">Time In</label>
                        <input type="time" name="time_in" required class="bw-input w-full p-3 border border-black focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-black mb-1">Time Out</label>
                        <input type="time" name="time_out" required class="bw-input w-full p-3 border border-black focus:ring-0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-black mb-1">Notes</label>
                    <textarea name="notes" rows="3" placeholder="What did you work on?" class="bw-input w-full p-3 border border-black focus:ring-0"></textarea>
                </div>
                <div class="pt-4">
                    <button type="submit" class="bw-button w-full py-4 bg-black text-white font-bold uppercase tracking-widest">
                        Save Time Log
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
