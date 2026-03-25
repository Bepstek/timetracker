<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJT Time Tracker</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/icon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #ffffff;
        }
        ::-webkit-scrollbar-thumb {
            background: #000000;
            border: 2px solid #ffffff;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #333333;
        }

        /* Glass Panel / High Contrast Theme Utilities */
        .bw-panel {
            background-color: #ffffff;
            border: 1px solid #000000;
        }
        .bw-button {
            background-color: #000000;
            color: #ffffff;
            transition: all 0.2s ease;
        }
        .bw-button:hover {
            background-color: #333333;
            transform: translateY(-1px);
        }
        .bw-input {
            border: 1px solid #e4e4e7;
            transition: border-color 0.2s ease;
        }
        .bw-input:focus {
            border-color: #000000;
            outline: none;
            box-shadow: none;
        }
    </style>
</head>
<body class="h-full text-black">
    <div class="min-h-full flex flex-col">
        <nav class="bg-white border-b border-black" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <img src="{{ asset('images/navbar.png') }}" alt="OJT Tracker Logo" class="h-10 w-auto">
                        </div>
                        @auth
                        <div class="hidden sm:-my-px sm:ml-10 sm:flex sm:space-x-8">
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold uppercase tracking-wide transition duration-150 ease-in-out">
                                Dashboard
                            </a>
                            <a href="{{ route('logs') }}" class="{{ request()->routeIs('logs') ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black hover:border-gray-300' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold uppercase tracking-wide transition duration-150 ease-in-out">
                                History
                            </a>
                        </div>
                        @endauth
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        @auth
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <div>
                                    <button @click="open = !open" type="button" class="bg-white flex text-sm focus:outline-none" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <div class="flex items-center">
                                            <span class="mr-3 text-sm font-medium text-black">{{ Auth::user()->name }}</span>
                                            <div class="h-8 w-8 border border-black flex items-center justify-center text-black font-bold uppercase bg-gray-50">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 border border-black shadow-none py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" style="display: none;">
                                    <div class="px-4 py-3 text-xs text-gray-500 border-b border-gray-100">
                                        Signed in as <br> <span class="font-bold text-black truncate">{{ Auth::user()->email }}</span>
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-black hover:text-white transition-colors duration-150" role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="flex space-x-4">
                                <a href="{{ route('login') }}" class="text-black hover:text-gray-600 px-3 py-2 text-sm font-bold uppercase tracking-wide">Log in</a>
                                <a href="{{ route('register') }}" class="bw-button px-5 py-2 text-sm font-bold uppercase tracking-wide shadow-none">Sign up</a>
                            </div>
                        @endauth
                    </div>
                    
                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = !open" type="button" class="bg-white inline-flex items-center justify-center p-2 text-black hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-black" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="sm:hidden border-t border-gray-200" id="mobile-menu" x-show="open" style="display: none;">
                @auth
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-black text-white' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-black' }} block pl-3 pr-4 py-2 text-base font-bold uppercase">Dashboard</a>
                    <a href="{{ route('logs') }}" class="{{ request()->routeIs('logs') ? 'bg-black text-white' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-black' }} block pl-3 pr-4 py-2 text-base font-bold uppercase">History</a>
                </div>
                <div class="pt-4 pb-4 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 border border-black flex items-center justify-center text-black font-bold uppercase">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-black">{{ Auth::user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-black hover:bg-gray-50">Sign out</button>
                        </form>
                    </div>
                </div>
                @else
                <div class="pt-4 pb-4 border-t border-gray-200">
                    <div class="space-y-1 px-2">
                        <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-black hover:bg-gray-50">Log in</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-black hover:bg-gray-50">Sign up</a>
                    </div>
                </div>
                @endauth
            </div>
        </nav>

        <main class="flex-grow py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-8 p-4 border border-black flex items-center bg-gray-50">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-black">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-8 p-4 border border-black bg-gray-50">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-black uppercase tracking-wide">Submission Errors</h3>
                                <div class="mt-2 text-sm text-gray-600">
                                    <ul role="list" class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
        
        <footer class="border-t border-gray-200 py-8 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-sm text-gray-500 uppercase tracking-widest font-semibold">&copy; {{ date('Y') }} OJT Tracker System.</p>
            </div>
        </footer>
    </div>
</body>
</html>
