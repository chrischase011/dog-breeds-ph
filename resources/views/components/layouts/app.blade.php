<x-layouts.base>
    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- Desktop view --}}
    <x-nav full-width class="hidden lg:block w-full mx-auto max-w-screen-2xl shadow-lg mb-3">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions class="justify-end w-full pb-5">
            <x-button label="Home" icon="mdi.home" link="{{ route('home') }}" class="btn-ghost btn-sm" responsive />
            <x-button label="Breeds" icon="mdi.dog" link="{{ route('breeds.list') }}" class="btn-ghost btn-sm" responsive />

            <div class="flex items-center gap-1">
                <x-popover>
                    <x-slot:trigger >
                        @if(auth()->user()->profile_picture)
                            <x-avatar :image="asset('storage/'. auth()->user()->profile_picture)" class="cursor-pointer">
                                <x-slot:title class="text-sm !font-bold">
                                    {{ auth()->user()->name }}
                                </x-slot:title>
                            </x-avatar>
                        @else
                            <x-avatar placeholder="{{ auth()->user()->initials() }}" class="cursor-pointer">
                                <x-slot:title class="text-sm !font-bold">
                                    {{ auth()->user()->name }}
                                </x-slot:title>
                            </x-avatar>
                        @endif
                    </x-slot:trigger>
                    <x-slot:content>
                        <x-menu class="p-0">
                            <x-menu-item title="Profile" icon="mdi.account" link="{{ route('profile') }}"/>
                            <x-menu-item 
                                title="Toggle Theme" 
                                icon="o-swatch" 
                                @click="$dispatch('mary-toggle-theme')"
                            />
                            <x-menu-separator />
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-button type="submit" class="btn-ghost w-full flex items-center justify-start hover:bg-black/10 dark:hover:bg-white/10">
                                    <div class="flex items-center justify-start gap-2">
                                        <x-icon name="mdi.logout" />
                                        <span>Logout</span>
                                    </div>
                                </x-button>
                            </form>
                        </x-menu>
                    </x-slot:content>
                </x-popover>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main with-nav>
        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    <script>
        window.Laravel = {
            dogApiUrl: "{{ config('app.dog_api') }}",
        };
    </script>
</x-layouts.base>