<div x-data="{ open: false }">

<div class="flex h-20 items-center bg-zinc-100 px-12">
    <div class="basis-1/2">
        <a href="{{ route('admin') }}" title="Dashboard">
            <img src="{{ asset('assets/imgs/lamborghini_logo.svg') }}" class="w-12 h-12" />
        </a>
    </div>
    <div class="basis-1/2 flex place-content-end block w-full lg:hidden">
        <div class="w-6 space-y-1 cursor-pointer" x-show="!open" x-cloak @click="open = true">
            <span class="block w-6 h-1 bg-gray-600"></span>
            <span class="block w-6 h-1 bg-gray-600"></span>
            <span class="block w-6 h-1 bg-gray-600"></span>
        </div>
        <div class="w-6 space-y-1 cursor-pointer rounded text-center" x-show="open" x-cloak @click="open = false">
            <span class="text-2xl font-bold">X</span>
        </div>
    </div>
</div>
<nav x-cloak x-show="open" class="mobile_menu fixed top-0 z-50 lg:position:relative lg:block flex items-start flex-wrap bg-stone-50 h-screen border-r border-stone-300">
    <div class="p-6 flex flex-col justify-start w-full h-full">
        <div class="grow">
            <div class="text-sm lg:flex-grow">
                <ul>
                    <li>
                        <x-navigation.page></x-navigation.page>
                    </li>
                </ul>
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
        <div class="mt-4">
            <x-logout></x-logout>
        </div>
        <div class="mt-4">
            <span class="text-sm">v0.0.1</span>
        </div>
    </div>
</nav>

</div>
