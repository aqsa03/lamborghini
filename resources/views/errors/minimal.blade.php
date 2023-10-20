<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        @vite(['resources/css/app.css'])

    </head>
    <body class="antialiased">
        <div class="error_page mt-[10vh] py-12">
            <div class="w-full">
                <a href="{{ route('admin') }}" title="Dashboard">
                    <img src="{{ asset('assets/imgs/lamborghini_logo.svg') }}" class="w-24 h-24 m-auto" />
                </a>
            </div>
            <div class="p-12">
                <div class="text-center pt-8">
                    <div class="text-7xl text-gray-500 font-bold pb-6">
                        @yield('code')
                    </div>

                    <div class="text-xl text-gray-700 uppercase pb-12">
                        @yield('message')
                    </div>

                    <div class="text-base text-black font-bold pb-6">
                        <a href="{{ route('admin') }}" class="text-blue-500 underline" title="CMS Dashboard">
                            CMS Dashboard
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </body>
</html>
