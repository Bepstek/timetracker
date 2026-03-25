@extends('app')

@section('content')
<div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
    <div class="w-full max-w-md bg-white border border-black p-10">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-black text-black uppercase tracking-widest">Create Account</h2>
            <div class="h-1 w-10 bg-black mx-auto mt-4"></div>
        </div>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-xs font-bold text-black uppercase tracking-widest mb-2" for="name">
                    Full Name
                </label>
                <input class="bw-input block w-full py-3 px-4 text-black bg-white focus:outline-none" id="name" type="text" name="name" placeholder="FULL NAME" required autofocus>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-black uppercase tracking-widest mb-2" for="email">
                    Email Address
                </label>
                <input class="bw-input block w-full py-3 px-4 text-black bg-white focus:outline-none" id="email" type="email" name="email" placeholder="USER@EXAMPLE.COM" required>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-black uppercase tracking-widest mb-2" for="password">
                    Password
                </label>
                <input class="bw-input block w-full py-3 px-4 text-black bg-white focus:outline-none" id="password" type="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="mb-8">
                <label class="block text-xs font-bold text-black uppercase tracking-widest mb-2" for="password_confirmation">
                    Confirm Password
                </label>
                <input class="bw-input block w-full py-3 px-4 text-black bg-white focus:outline-none" id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required>
            </div>

            <div class="flex items-center justify-between mb-8">
                <button class="bw-button w-full py-4 text-sm font-bold uppercase tracking-widest border border-black" type="submit">
                    Register Account
                </button>
            </div>

             <div class="text-center">
                <a class="text-xs text-gray-500 uppercase tracking-widest hover:text-black hover:underline" href="{{ route('login') }}">
                    Already have an account?
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
