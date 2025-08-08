<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;
use App\Models\User;
use App\Models\UserDogChoice;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.auth')] class extends Component {
    use Toast;
    public string $email = '';
    public string $password = '';

    public function login()
    {
        try {
            $this->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $credentials = [
                'email' => $this->email,
                'password' => $this->password,
            ];

            if(Auth::attempt($credentials)) {
                $this->success('Login Successful', 'Welcome back to Dog Breed PH!');
                $this->reset();

                $userDogChoice = UserDogChoice::where('user_id', Auth::id())->first();

                if(!$userDogChoice) {
                    redirect()->route('select.breeds');
                }
                else { 
                    redirect()->route('home');
                }
            } else {
                $this->error('Login Failed', 'Invalid email or password.');
                return;
            }
        }
        catch( \Illuminate\Validation\ValidationException $e) {
            $this->error('Validation Error', $e->getMessage());
            return;
        }
        catch(\Exception $e) {
            $this->error('Login Failed', 'An unexpected error occurred. Please try again later.');
            return;
        }
    }

}; ?>

<div class="flex flex-col items-center justify-center h-screen">
   <x-card title="Dog Breed PH Login" subtitle="Join the Dog Lovers Community" class="w-full max-w-3xl border border-gray-400" shadow separator>
    <form wire:submit.prevent="login" class="space-y-4">
        <div>
            <x-input label="Email" type="email" wire:model.defer="email" required />
        </div>
        <div>
            <x-input label="Password" type="password" wire:model.defer="password" required />
        </div>
        <div class="flex flex-col items-center justify-between gap-4">
            <x-button type="submit" class="w-full btn-primary">Login</x-button>
            <x-button link="{{ route('register') }}" class="w-full btn-ghost hover:underline">Register Here</x-button>
        </div>
    </x-card>
</div>
