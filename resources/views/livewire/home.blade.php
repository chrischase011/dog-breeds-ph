<?php

use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Support\Collection;

new class extends Component {
    public Collection $users;
    public Collection $userDogChoices;

    public function mount()
    {
        $this->users = User::with('userDogChoices')->get();
        $this->userDogChoices = $this->users->pluck('userDogChoices')->flatten();

        Debugbar::info('User data loaded', [
            'user' => $this->users,
            'userDogChoices' => $this->userDogChoices,
        ]);
    }
}; ?>

<div>
    <x-header title="Dog Lovers Community" subtitle="Explore our community of dog lovers" separator />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        @foreach ($users as $user)
            <div
                class="flex items-center p-4 border border-gray-600 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <x-avatar :image="asset('storage/' . $user->profile_picture)" class="!w-14 !h-14 shrink-0" />

                <div class="ml-4">
                    {{-- Name --}}
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        {{ $user->name }}
                    </div>

                    {{-- Dog breeds --}}
                    <div class="text-sm text-blue-600 mt-1 flex flex-wrap gap-2">
                        @forelse($user->userDogChoices as $choice)
                            <x-popover>
                                <x-slot:trigger>
                                    <span class="underline cursor-pointer">
                                        {{ $choice->dog_breed }}
                                    </span>
                                </x-slot:trigger>
                                <x-slot:content>
                                    <div x-data="{ img: null }" x-init="fetch(`https://dog.ceo/api/breed/{{ strtolower(str_replace(' ', '-', $choice->dog_breed)) }}/images`)
                                        .then(res => res.json())
                                        .then(data => img = data.message[0] || null)">
                                        <template x-if="img">
                                            <img x-bind:src="img" class="w-24 h-24 rounded" />
                                        </template>
                                        <template x-if="!img">
                                            <span>No image found</span>
                                        </template>
                                    </div>
                                </x-slot:content>
                            </x-popover>
                        @empty
                            <span class="text-gray-500">No breeds selected</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
