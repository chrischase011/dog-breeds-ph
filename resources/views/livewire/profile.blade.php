<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $profile_picture = '';
    public string $address = '';
    public string $phone = '';
    public $dogs;

    public $new_profile_picture;

    public bool $openEditProfile = false;

    public function mount()
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
        $this->profile_picture = asset('storage/' . auth()->user()->profile_picture);
        $this->address = auth()->user()->address ?? '';
        $this->phone = '+63'.auth()->user()->phone ?? '';
        $this->dogs = auth()->user()->userDogChoices->pluck('dog_breed');
    }   

    public function updateProfile()
    {
        try {
            DB::beginTransaction();
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . auth()->id(),
                'new_profile_picture' => 'nullable|image|max:1024',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
            ]);

            $user = auth()->user();
            $user->name = $this->name;
            $user->email = $this->email;
            $user->address = $this->address;
            $user->phone = $this->phone;

            if ($this->new_profile_picture) {
                $profilePicturePath = $this->new_profile_picture->storePubliclyAs(
                    path: 'profile_pictures',
                    name: Str::slug($this->name) . '_' . Str::uuid() . '-'. time() . '.' . $this->profile_picture->extension(),
                    options: 'public'
                );
                $user->profile_picture = $profilePicturePath;
            }

            $user->save();

            DB::commit();
            $this->success('Profile Updated', 'Your profile has been successfully updated.');

            $this->openEditProfile = false;
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Validation Error', $e->getMessage());
            DB::rollBack();
            return;
        }
        catch (\Exception $e) {
            $this->error('Update Failed', 'An unexpected error occurred. Please try again later.');
            DB::rollBack();
            return;
        }
    }

}; ?>

<div>
    <x-card title="Profile Information" class="w-full max-w-2xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <x-avatar :image="$profile_picture" class="w-24 h-24" />
                <div>
                    <h2 class="text-xl font-bold">{{ $name }}</h2>
                    <p class="text-sm text-gray-400"><b>{{ $email }}</b></p>
                    <p class="text-sm text-gray-400">Contact: <b>{{ $phone !== '' ? $phone : 'Not provided' }}</b></p>
                    <p class="text-sm text-gray-400">Address: <b>{{ $address !== '' ? $address : 'Not provided' }}</b></p>
                    <p class="text-sm text-gray-400">Member since: <b>{{ auth()->user()->created_at->format('F j, Y') }}</b></p>
                </div>
            </div>
            <div>
                <x-button label="Edit Profile" icon="mdi.pencil" class="btn-warning" @click="$wire.openEditProfile = true" />
            </div>
        </div>

        <div class="mt-4">
            <h3 class="text-lg font-bold">My Selected Dog Breeds</h3>
            <div class="flex items-center gap-4 mt-3">
                @foreach($dogs as $dog)
                    <x-popover>
                        <x-slot name="trigger">
                            <span class="cursor-pointer underline">{{ $dog }}</span>
                        </x-slot>

                        <x-slot name="content">
                            <div x-data="{ img: null }" x-init="fetch(`https://dog.ceo/api/breed/{{ strtolower(str_replace(' ', '-', $dog)) }}/images`)
                                .then(res => res.json())
                                .then(data => img = data.message[0] || null)">
                                <template x-if="img">
                                    <img x-bind:src="img" class="w-28 h-28 rounded" />
                                </template>
                                <template x-if="!img">
                                    <span>No image found</span>
                                </template>
                            </div>
                        </x-slot>
                    </x-popover>
            @endforeach
            </div>
        </div>
    </x-card>

    <x-modal wire:model="openEditProfile" title="Edit Profile" separator>
        <x-form wire:submit.prevent="updateProfile">
            <div class="space-y-4">
                <div class="flex justify-center">
                <x-file wire:model="new_profile_picture" accept="image/png" crop-after-change change-text="Select Profile Picture" crop-title-text="Crop Profile Picture">
                    <img src="{{ $profile_picture ?? $new_profile_picture }}" class="h-40 rounded-lg" />
                </x-file>
            </div>
                <x-input label="Name" wire:model.defer="name" required />
                <x-input label="Email" type="email" wire:model.defer="email" required />
                <x-input label="Contact Number" wire:model.defer="phone" prefix="+63" maxlength="10" placeholder="Enter contact number" x-data x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')" />
                <x-input label="Address" wire:model.defer="address" />
            </div>
            <x-slot:actions>
                <x-button label="Cancel" class="btn-ghost" @click="$wire.openEditProfile = false" />
                <x-button type="submit" label="Update Profile" wire:target="updateProfile" wire:loading.attr="disabled" spinner="updateProfile" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
