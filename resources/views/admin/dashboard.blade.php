@extends('admin.layout')

@section('content')
<div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h1 class="text-4xl font-black uppercase tracking-tight text-black">OJT Rooms</h1>
        <div class="flex mt-3 space-x-4">
            <a href="{{ route('admin.dashboard', ['status' => 'active']) }}" 
               class="text-[10px] font-black uppercase tracking-widest pb-1 border-b-2 {{ $status === 'active' ? 'border-black text-black' : 'border-transparent text-gray-400 hover:text-black' }}">
               Active Sessions
            </a>
            <a href="{{ route('admin.dashboard', ['status' => 'archived']) }}" 
               class="text-[10px] font-black uppercase tracking-widest pb-1 border-b-2 {{ $status === 'archived' ? 'border-black text-black' : 'border-transparent text-gray-400 hover:text-black' }}">
               Archived Rooms
            </a>
        </div>
    </div>
    <button onclick="document.getElementById('addCompanyModal').classList.remove('hidden')" class="bg-black text-white px-8 py-4 text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-colors">
        Open New Room
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($companies as $company)
        <div class="bw-panel relative group">
            <a href="{{ route('admin.companies.show', $company) }}" class="block p-8 pr-16">
                <div class="flex justify-between items-start mb-6">
                    <div class="bg-gray-100 px-3 py-1 text-[9px] font-black uppercase tracking-tighter text-gray-600">
                        {{ $company->batch }}
                    </div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $company->users_count }} Students
                    </div>
                </div>
                
                <h3 class="text-2xl font-black text-black uppercase leading-none mb-2 group-hover:underline">{{ $company->name }}</h3>
                <p class="text-gray-500 text-[11px] font-medium uppercase tracking-wider mb-8 truncate">{{ $company->address ?: 'OFF-SITE / REMOTE' }}</p>
                
                <div class="flex items-center text-[10px] font-black uppercase tracking-widest text-black group-hover:translate-x-2 transition-transform">
                    Enter Room 
                    <svg class="h-3 w-3 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </a>
            
            <!-- Quick Archive Toggle -->
            <form action="{{ route('admin.companies.archive', $company) }}" method="POST" class="absolute top-8 right-8 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                @csrf
                @method('PATCH')
                <button type="submit" class="p-2 text-gray-300 hover:text-red-600 hover:bg-gray-50 rounded transition-all" title="{{ $company->is_archived ? 'Restore Room' : 'Archive Room' }}">
                    @if($company->is_archived)
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    @else
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                    @endif
                </button>
            </form>
        </div>
    @endforeach
</div>

@if($companies->isEmpty())
    <div class="bw-panel py-20 text-center border-dashed border-2">
        <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No {{ $status }} OJT Rooms found.</p>
    </div>
@endif

<!-- Add Company Modal -->
<div id="addCompanyModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg p-10 relative">
        <button onclick="document.getElementById('addCompanyModal').classList.add('hidden')" class="absolute top-6 right-6 text-black hover:rotate-90 transition-transform">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        
        <h3 class="text-3xl font-black uppercase tracking-tight mb-8">Setup New OJT Room</h3>
        
        <form action="{{ route('admin.companies.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Company Name</label>
                    <input type="text" name="name" required placeholder="THE AGENCY NAME" class="w-full p-4 border border-gray-200 focus:border-black outline-none font-bold uppercase text-sm">
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Batch Period</label>
                        <input type="text" name="batch" required placeholder="2025-2025" class="w-full p-4 border border-gray-200 focus:border-black outline-none font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Contact Person</label>
                        <input type="text" name="contact_person" placeholder="SUPERVISOR NAME" class="w-full p-4 border border-gray-200 focus:border-black outline-none font-bold text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Office Address</label>
                    <input type="text" name="address" placeholder="PHYSICAL OR REMOTE" class="w-full p-4 border border-gray-200 focus:border-black outline-none font-bold text-sm">
                </div>
                <div class="pt-6">
                    <button type="submit" class="w-full py-5 bg-black text-white font-black uppercase tracking-widest hover:bg-gray-800 transition-all">
                        Initialize Room
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
