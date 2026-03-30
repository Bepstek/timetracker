@extends('app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-10">
        <h1 class="text-5xl font-black uppercase tracking-tight text-black leading-none mb-3">Account Settings</h1>
        <p class="text-gray-500 font-bold uppercase text-[11px] tracking-widest flex items-center">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            Update your password and security credentials
        </p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4">
            <p class="text-xs font-black text-green-700 uppercase">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
            <ul class="text-[10px] font-black text-red-600 uppercase tracking-widest list-none">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bw-panel border-2 border-black p-8">
        <form action="{{ route('settings.password') }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Current Password</label>
                    <input type="password" name="current_password" required class="w-full bg-gray-50 border-2 border-black p-4 text-sm font-bold focus:bg-white focus:outline-none transition-all">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">New Password</label>
                        <input type="password" name="password" required class="w-full bg-gray-50 border-2 border-black p-4 text-sm font-bold focus:bg-white focus:outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border-2 border-black p-4 text-sm font-bold focus:bg-white focus:outline-none transition-all">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-black text-white px-8 py-5 text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-all border-2 border-black">
                        Change Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
