<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TV2000 CMS</title>
        @vite(['resources/css/welcome.css'])
    </head>
    <body class="min-h-screen flex flex-col place-items-center place-content-center p-12">
        {{-- <div class="">
            @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div> --}}
        <x-auth.login_form>
            <div class="m-8">
                <img src="{{ asset('assets/imgs/lamborghini_logo.svg') }}" class="w-20 h-20" />
            </div>
        </x-auth.login_form>
    </body>
</html>
