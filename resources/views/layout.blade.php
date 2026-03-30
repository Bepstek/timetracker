<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Time Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <nav class="bg-white shadow mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="font-bold text-xl text-blue-600">OJT Tracker</a>
                    </div>
                    @auth
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        @can('isAdmin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'border-blue-500 text-gray-900 border-b-2' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Admin Dashboard
                        </a>
                        <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'border-blue-500 text-gray-900 border-b-2' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                            User Logs
                        </a>
                        @else
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900 border-b-2' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('logs') }}" class="{{ request()->routeIs('logs') ? 'border-blue-500 text-gray-900 border-b-2' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Logs
                        </a>
                        @endcan
                    </div>
                    @endauth
                </div>
                <div class="flex items-center">
                    @auth
                        <div class="relative ml-3" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open" type="button" class="flex items-center max-w-xs bg-white text-sm focus:outline-none group" id="user-menu-button">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="flex flex-col items-end mr-3">
                                        <span class="text-xs font-black text-black uppercase tracking-widest group-hover:text-blue-600 transition-colors">{{ Auth::user()->name }}</span>
                                        @if(Auth::user()->role === 'admin')
                                            <span class="text-[8px] bg-black text-white px-1 font-black">ADMIN</span>
                                        @endif
                                    </div>
                                    <svg class="h-5 w-5 text-gray-400 group-hover:text-black transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-48 bg-white border-2 border-black focus:outline-none z-50">
                                <div class="py-1">
                                    <a href="{{ route('settings') }}" class="block px-4 py-3 text-[10px] font-black uppercase tracking-widest text-black hover:bg-gray-50 border-b border-gray-100 flex items-center">
                                        <svg class="h-3 w-3 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        Settings
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left block px-4 py-3 text-[10px] font-black uppercase tracking-widest text-red-600 hover:bg-red-50 flex items-center">
                                            <svg class="h-3 w-3 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-900 mr-4">Login</a>
                        <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:text-blue-900">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t mt-12 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} OJT Time Tracker.
        </div>
    </footer>

</body>
</html>
