<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

new #[Layout('components.layouts.auth')] class extends Component {
    use Toast, WithFileUploads;

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $name = '';
    public $profilePicture;

    public string $emptyUserJpg;

    public function mount()
    {
        $this->emptyUserJpg = asset('images/empty-user.jpg');
    }

    public function register()
    {
        try {
            DB::beginTransaction();

            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'profilePicture' => 'required|image|max:1024', // 1MB max
            ]);

            $profilePicturePath = $this->profilePicture->storePubliclyAs(
                path: 'profile_pictures',
                name: Str::slug($this->name) . '_' . Str::uuid() . '-'. time() . '.' . $this->profilePicture->extension(),
                options: 'public'
            );

            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'profile_picture' => $profilePicturePath,
            ]);

            DB::commit();
            $this->success('Registration Successful', 'Welcome to Dog Breed PH!');

            redirect()->route('login');

        }
        catch (\Illuminate\Validation\ValidationException $e) {
            $this->error('Validation Error', $e->getMessage());
            DB::rollBack();
            return;
        }
        catch(\Exception $e) {
            $this->error('Registration Failed', 'An unexpected error occurred. Please try again later.');
            DB::rollBack();
            return;
        }
    }
}; ?>

<div class="flex flex-col items-center justify-center h-screen">
    <x-card title="Dog Breed PH Registration" subtitle="Join the Dog Lovers Community" class="w-full max-w-3xl border border-gray-400" shadow separator>
        <form wire:submit.prevent="register" class="space-y-4">
            <div class="flex justify-center">
                <x-file wire:model="profilePicture" accept="image/png" crop-after-change change-text="Select Profile Picture" crop-title-text="Crop Profile Picture">
                    <img src="{{ $profilePicture ?? $emptyUserJpg }}" class="h-40 rounded-lg" />
                </x-file>
            </div>
            <div>
                <x-input label="Name" wire:model.defer="name" required />
            </div>
            <div>
                <x-input label="Email" type="email" wire:model.defer="email" required />
            </div>
            <div>
                <x-input label="Password" type="password" wire:model.defer="password" required />
            </div>
            <div>
                <x-input label="Confirm Password" type="password" name="password_confirmation" wire:model.defer="password_confirmation" required />
            </div>
            <div class="flex flex-col items-center justify-between gap-4">
                <x-button type="submit" wire:target="profilePicture" wire:loading.attr="disabled" spinner="register" class="w-full btn-primary">Register</x-button>
                <x-button link="{{ route('login') }}" class="w-full btn-ghost hover:underline">Login Here</x-button>
            </div>
        </form>
    </x-card>
</div>

@push('external-styles-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
@endpush