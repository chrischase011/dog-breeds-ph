<?php

use Livewire\Volt\Component;

new class extends Component {
    public int $page = 1;
    public int $lastPage = 1;

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
            $this->emitUp('pageChanged', $this->page);
        }
    }

    public function nextPage()
    {
        if ($this->page < $this->lastPage) {
            $this->page++;
            $this->emitUp('pageChanged', $this->page);
        }
    }
}; ?>

<div class="flex justify-center mt-6 space-x-4">
    <button wire:click="previousPage" @disabled($page <= 1)
        class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50">Previous</button>

    <span>Page {{ $page }} of {{ $lastPage }}</span>

    <button wire:click="nextPage" @disabled($page >= $lastPage)
        class="px-4 py-2 bg-gray-300 rounded disabled:opacity-50">Next</button>
</div>