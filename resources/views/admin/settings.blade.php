@extends('admin.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-10">
        <h1 class="text-5xl font-black uppercase tracking-tight text-black leading-none mb-3">Admin Security</h1>
        <p class="text-gray-500 font-bold uppercase text-[11px] tracking-widest flex items-center">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            Update your administrative password
        </p>
    </div>

    <!-- The same form code but with Admin-specific styling if needed, or simply keeping the flow -->
    <div class="bg-white border-2 border-black p-8 shadow-[10px_10px_0px_0px_rgba(0,0,0,1)]">
        <form action="{{ route('settings.password') }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Current Admin Password</label>
                    <input type="password" name="current_password" required class="w-full bg-gray-50 border-2 border-black p-4 text-sm font-bold focus:bg-white focus:outline-none transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">New Admin Password</label>
                        <input type="password" name="password" required class="w-full bg-gray-50 border-2 border-black p-4 text-sm font-bold focus:bg-white focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Confirm Admin Password</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border-2 border-black p-4 text-sm font-bold focus:bg-white focus:outline-none transition-all">
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 bg-black text-white px-8 py-5 text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-all border-2 border-black">
                        Update Security Credentials
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="flex-1 border-2 border-black text-black text-center px-8 py-5 text-xs font-black uppercase tracking-widest hover:bg-black hover:text-white transition-all">
                        Discard
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
