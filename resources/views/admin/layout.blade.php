<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - OJT Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#f0f2f5] text-gray-900 font-sans">

    <!-- Admin Top Navbar -->
    <nav class="bg-black text-white py-4 px-8 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <a href="{{ route('admin.dashboard') }}" class="font-black text-xl tracking-tighter">ADMIN PANEL</a>
                <div class="hidden md:flex space-x-6 text-[10px] font-bold uppercase tracking-widest">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400 hover:text-white' }}">OJT Rooms</a>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'text-white' : 'text-gray-400 hover:text-white' }}">User Management</a>
                    <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'text-white' : 'text-gray-400 hover:text-white' }}">Global Logs</a>
                </div>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="flex items-center space-x-3 group focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-[10px] font-black uppercase tracking-tighter leading-none group-hover:text-gray-300 transition-colors">Logged in as</p>
                            <p class="text-xs font-bold text-gray-300 group-hover:text-white transition-colors">{{ Auth::user()->name }}</p>
                        </div>
                        <svg class="h-4 w-4 text-gray-500 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         class="origin-top-right absolute right-0 mt-4 w-48 bg-white border-2 border-black z-50 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]">
                        <div class="py-1">
                            <a href="{{ route('settings') }}" class="block px-4 py-3 text-[10px] font-black uppercase tracking-widest text-black hover:bg-gray-50 border-b-2 border-black flex items-center">
                                <svg class="h-3 w-3 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Admin Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-3 text-[10px] font-black uppercase tracking-widest text-red-600 hover:bg-red-50 flex items-center">
                                    <svg class="h-3 w-3 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    Term. Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if(session('success'))
            <div class="mb-6 bg-white border-l-4 border-green-500 p-4 shadow-sm">
                <p class="text-sm font-bold text-gray-700">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-white border-l-4 border-red-500 p-4 shadow-sm">
                <ul class="text-sm font-bold text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-20 py-10 border-t border-gray-200 text-center">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">&copy; {{ date('Y') }} OJT Tracker Admin System.</p>
    </footer>

    <style>
        .bw-panel {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .bw-button {
            transition: all 0.2s ease;
        }
        .bw-input {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0;
        }
    </style>
</body>
</html>