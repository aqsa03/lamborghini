<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Play2000 CMS</title>
        <link rel="shortcut icon" href="{{ asset('assets/imgs/favicon.ico') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css'])
        @stack('headstyles')

        <script>
            const BASE_URL = '{{ url("/") }}';
        </script>
        @stack('headscripts')
    </head>
    <body>
        <div class="block lg:hidden border-b border-stone-300">
            <x-navigation_mobile></x-navigation_mobile>
        </div>
        <div class="flex">
            <div class="lg:basis-[280px] lg:min-w-[280px]">
                <x-navigation></x-navigation>
            </div>
            <div class="grow">
                <main class="top_main lg:h-screen overflow-y-auto overflow-x-hidden">
                    @if(Session::has('success'))
                        <x-alerts.success>{{ Session::get('success') }}</x-alerts.success>
                    @endif
                    @if(Session::has('error'))
                        <x-alerts.error>{{ Session::get('error') }}</x-alerts.error>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
    @vite(['resources/js/app.js'])
    @stack('scripts')
</html>
