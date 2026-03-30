@extends('admin.layout')

@section('content')
<!-- Reset Result Modal -->
@if(session('reset_password_data'))
<div id="resetResultModal" class="fixed inset-0 bg-black/90 backdrop-blur-md z-[100] flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md border-4 border-black p-8 shadow-[15px_15px_0px_0px_rgba(255,255,255,0.1)]">
        <div class="text-center mb-6">
            <div class="h-16 w-16 bg-black text-white rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
            </div>
            <h2 class="text-2xl font-black uppercase tracking-tighter">Password Reset Complete</h2>
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mt-2">Write this down - it will not be shown again</p>
        </div>

        <div class="bg-gray-50 border-2 border-black p-6 mb-8 text-center">
            <p class="text-[10px] font-black uppercase text-gray-400 mb-2">New Password for {{ session('reset_password_data')['name'] }}</p>
            <div class="text-4xl font-black tracking-widest text-black select-all font-mono">
                {{ session('reset_password_data')['password'] }}
            </div>
        </div>

        <button onclick="document.getElementById('resetResultModal').remove()" class="w-full bg-black text-white py-4 text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-all border-2 border-black">
            I've written it down / Close
        </button>
    </div>
</div>
@endif

<div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div>
        <h1 class="text-5xl font-black uppercase tracking-tight text-black leading-none mb-3">User Management</h1>
        <p class="text-gray-500 font-bold uppercase text-[11px] tracking-widest flex items-center">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            Manage application users and roles
        </p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white border-2 border-black p-6 mb-8">
    <form action="{{ route('admin.users') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Search User</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Email..." class="w-full bg-gray-50 border-2 border-black p-3 text-xs font-bold focus:bg-white focus:outline-none transition-colors">
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Filter Role</label>
            <select name="role" class="w-full bg-gray-50 border-2 border-black p-3 text-xs font-bold focus:bg-white focus:outline-none transition-colors">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-black text-white px-6 py-3 text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition-colors">
                Apply Filters
            </button>
        </div>
    </form>
</div>

<!-- User Table -->
<div class="bw-panel overflow-hidden border-2 border-black">
    <div class="px-8 py-6 border-b-2 border-black flex justify-between items-center bg-gray-50">
        <h3 class="text-xs font-black text-black uppercase tracking-[0.2em]">System Users</h3>
        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $users->total() }} Total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-left">
            <thead>
                <tr class="bg-white border-b-2 border-black">
                    <th class="px-8 py-5 text-[10px] font-black text-black uppercase tracking-widest">User Details</th>
                    <th class="px-8 py-5 text-[10px] font-black text-black uppercase tracking-widest">Role</th>
                    <th class="px-8 py-5 text-[10px] font-black text-black uppercase tracking-widest">Assignment</th>
                    <th class="px-8 py-5 text-[10px] font-black text-black uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-8 py-6">
                        <div class="flex items-center">
                            <div class="h-10 w-10 {{ $user->role === 'admin' ? 'bg-black' : 'bg-gray-200' }} flex items-center justify-center text-{{ $user->role === 'admin' ? 'white' : 'black' }} font-black text-xs uppercase mr-4">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-black uppercase">{{ $user->name }}</p>
                                <p class="text-[10px] font-bold text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="inline-block px-2 py-1 text-[9px] font-black uppercase tracking-tighter {{ $user->role === 'admin' ? 'bg-black text-white' : 'bg-gray-100 text-gray-800' }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        @if($user->role === 'student')
                            @if($user->company)
                                <a href="{{ route('admin.companies.show', $user->company) }}" class="text-[10px] font-bold text-black border-b border-black hover:bg-black hover:text-white transition-colors">
                                    {{ $user->company->name }}
                                </a>
                            @else
                                <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest italic">Unassigned</span>
                            @endif
                        @else
                            <span class="text-[10px] font-bold text-gray-300 uppercase">—</span>
                        @endif
                    </td>
                    <td class="px-8 py-6 text-right flex items-center justify-end gap-3">
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.resetPassword', $user) }}" method="POST" onsubmit="return confirm('You are about to reset this users password to a random string. Proceed?')" class="inline">
                                @csrf
                                <button type="submit" class="text-[9px] font-black uppercase tracking-widest border border-black text-black hover:bg-black hover:text-white px-3 py-1.5 transition-all">
                                    Reset Password
                                </button>
                            </form>
                            <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('CRITICAL: Are you sure? This will delete the user account and all associated time logs permanently.')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] font-black uppercase tracking-widest border-b-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white px-2 py-1 transition-all">
                                    Delete User
                                </button>
                            </form>
                        @else
                            <span class="text-[10px] font-black uppercase text-gray-300">Self Account</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-8 py-6 border-t-2 border-black bg-gray-50">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
