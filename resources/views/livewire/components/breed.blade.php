<?php

use Livewire\Volt\Component;

new class extends Component {
    public mixed $name = '';
    public mixed $image = '';
}; ?>

<div class="flex flex-col items-center w-full p-2">
    <img 
        src="{{ $image }}" 
        alt="{{ $name }}" 
        class="w-32 h-32 object-cover rounded-lg shadow"
        loading="lazy"
    >

    <p class="mt-2 text-sm font-medium text-center">{{ $name }}</p>
</div>