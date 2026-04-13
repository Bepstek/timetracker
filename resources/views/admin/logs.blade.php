@extends('admin.layout')

@section('content')
<div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h1 class="text-4xl font-black uppercase tracking-tight text-black">Global Time Logs</h1>
        <p class="text-gray-500 font-bold uppercase text-[10px] tracking-[0.2em] mt-2">Audit Registry / All Student Records</p>
    </div>
</div>

<div class="bw-panel mb-8 p-6 bg-white border-2 border-black">
    <form action="{{ route('admin.logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">Student</label>
            <select name="user_id" class="w-full p-2 border border-gray-200 focus:border-black outline-none font-bold text-xs uppercase">
                <option value="">ALL STUDENTS</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1">Status</label>
            <select name="status" class="w-full p-2 border border-gray-200 focus:border-black outline-none font-bold text-xs uppercase">
                <option value="">ALL STATUSES</option>
                <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>ACTIVE</option>
                <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>COMPLETED</option>
            </select>
        </div>
        <div class="flex items-end gap-2 md:col-span-2">
            <button type="submit" class="bg-black text-white px-6 py-2 text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition-colors">
                Apply Filters
            </button>
            <a href="{{ route('admin.logs') }}" class="border border-black px-6 py-2 text-[10px] font-black uppercase tracking-widest hover:bg-black hover:text-white transition-colors text-center">
                Reset
            </a>
        </div>
    </form>
</div>

<div class="bw-panel overflow-hidden border-2 border-black">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="bg-gray-50 border-b-2 border-black">
                    <th class="px-8 py-4 text-[10px] font-black text-black uppercase tracking-widest">Student</th>
                    <th class="px-8 py-4 text-[10px] font-black text-black uppercase tracking-widest">Date / Time</th>
                    <th class="px-8 py-4 text-[10px] font-black text-black uppercase tracking-widest">Hours</th>
                    <th class="px-8 py-4 text-[10px] font-black text-black uppercase tracking-widest">Status</th>
                    <th class="px-8 py-4 text-[10px] font-black text-black uppercase tracking-widest">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-8 py-4">
                        <p class="text-[11px] font-black text-black uppercase">{{ $log->user->name }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $log->user->company->name ?? 'UNASSIGNED' }}</p>
                    </td>
                    <td class="px-8 py-4">
                        <p class="text-[11px] font-black text-black uppercase">{{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase">{{ \Carbon\Carbon::parse($log->time_in)->format('H:i') }} - {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : 'LIVE' }}</p>
                    </td>
                    <td class="px-8 py-4 text-sm font-black text-black tabular-nums">
                        @php
                            $hours = floor($log->total_hours);
                            $minutes = round(($log->total_hours - $hours) * 60);
                        @endphp
                        {{ $hours }}h {{ $minutes }}m
                    </td>
                    <td class="px-8 py-4">
                        <span class="text-[9px] font-black uppercase px-2 py-1 {{ $log->status === 'active' ? 'bg-black text-white' : 'bg-gray-100 text-gray-400' }}">
                            {{ $log->status }}
                        </span>
                    </td>
                    <td class="px-8 py-4 text-[10px] font-medium text-gray-500 max-w-xs truncate">
                        {{ $log->notes ?: '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-gray-300 font-bold uppercase tracking-widest text-sm">Registry is empty.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="px-8 py-4 bg-gray-50 border-t border-black">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
