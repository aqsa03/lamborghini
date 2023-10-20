<nav class="desktop_menu hidden lg:position:relative lg:block flex items-start flex-wrap bg-stone-50 h-max-content border-r border-stone-300">
    <div class="p-6 flex flex-col justify-start w-full h-screen">
        <div class="flex flex-col items-center mb-12">
            <a href="{{ route('admin') }}" title="Dashboard">
                <img src="{{ asset('assets/imgs/lamborghini_logo.svg') }}" class="w-12 h-12" />
            </a>
        </div>
        <div class="grow">
            <div class="text-sm lg:flex-grow">
                <!-- <ul>
                    <li>
                        <x-navigation.page></x-navigation.page>
                    </li>
                </ul> -->
                <ul>
                    <li>
                        <x-navigation.vod></x-navigation.vod>
                    </li>
                </ul>
                <ul>
                    <li>
                        <x-navigation.live></x-navigation.live>
                    </li>
                </ul>
                <ul>
                    <li>
                        <x-navigation.news></x-navigation.news>
                    </li>
                </ul>
                <ul>
                    <li>
                        <x-navigation.notifications></x-navigation.notifications>
                    </li>
                </ul>

                @if (Auth::user()->is_admin())
                <ul>
                    <li>
                        <x-navigation.users></x-navigation.users>
                    </li>
                </ul>
                <ul>
                    <li>
                        <x-navigation.members></x-navigation.members>
                    </li>
                </ul>
                @endif
            </div>
        </div>
        <div>
            <div class="mt-4">
                <x-logout></x-logout>
            </div>
            <div class="mt-4">
                <span class="text-sm">v0.0.1</span>
            </div>
        </div>
    </div>
</nav>
