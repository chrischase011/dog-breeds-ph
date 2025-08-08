<?php

use Livewire\Volt\Component;

new class extends Component {
    public array $breeds = [];

    protected $listeners = [
        'breedsFetched' => 'setBreeds',
        'selectBreed',
    ];

    public array $selectedBreeds = [];

    public function setBreeds(array $breeds)
    {
        $this->breeds = $breeds;
    }
};
?>

<div>
    <x-header title="Dog Breeds" subtitle="Explore the dog breeds" separator />

   <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @forelse ($this->breeds as $breed)
            <livewire:components.breed
                :key="$breed['name']"
                :name="$breed['name']"
                :image="$breed['image']"
            />
        @empty
            <p class="col-span-full text-center">Loading Data...</p>
        @endforelse
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            (async () => {
                const breeds = await window.dog.fetchDogBreeds();
                Livewire.dispatch('breedsFetched', { breeds });
            })();
        });
    </script>
</div>
