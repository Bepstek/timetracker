@extends('app')

@section('content')
<div class="bw-panel p-0 overflow-hidden">
    <div class="px-6 py-6 border-b border-black flex justify-between items-center bg-white">
        <h3 class="text-xl font-bold text-black uppercase tracking-wide">Time Logs History</h3>
        <a href="{{ route('logs.export') }}" class="bw-button px-5 py-2 text-xs font-bold uppercase tracking-widest border border-black flex items-center">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Export
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">In</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Out</th>
                    <th class="px-6 py-4 text-xs font-bold text-black uppercase tracking-widest">Total</th>
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
                        {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                    </td>
                    <td class="px-6 py-5 text-sm text-gray-500">
                        {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '—' }}
                    </td>
                    <td class="px-6 py-5 text-sm font-mono font-bold text-black">
                        {{ $log->total_hours }}
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
@endsection
