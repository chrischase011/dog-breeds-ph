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
            <div class="flex items-center p-4 border border-gray-600 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                {{-- Avatar --}}
                <x-avatar :image="asset('storage/' . $user->profile_picture)" class="!w-14 !h-14 shrink-0" />

                {{-- User info --}}
                <div class="ml-4">
                    {{-- Name --}}
                    <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                        {{ $user->name }}
                    </div>

                    {{-- Dog breeds --}}
                    <div class="text-sm text-blue-600 mt-1">
                        {{ $user->userDogChoices->pluck('dog_breed')->join(', ') ?: 'No breeds selected' }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
