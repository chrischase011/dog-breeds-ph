<?php

use Livewire\Volt\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\DB;
use App\Models\UserDogChoice;

new class extends Component {
    use Toast;

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

    public function selectBreed($breed)
    {
        if (in_array($breed, $this->selectedBreeds)) {
            $this->selectedBreeds = array_filter($this->selectedBreeds, fn($b) => $b !== $breed);
            return;
        }

        if (count($this->selectedBreeds) >= 3) {
            $this->error('Error', 'You can only select up to 3 breeds.');
            return;
        }

        $this->selectedBreeds[] = $breed;
    }

    public function saveSelection()
    {
        if (count($this->selectedBreeds) < 3) {
            $this->error('Error', 'Please select at least 3 breeds.');
            return;
        }

        try {
            DB::beginTransaction();

            $userId = auth()->id();

            UserDogChoice::where('user_id', $userId)
                ->whereNotIn('dog_breed', $this->selectedBreeds)
                ->delete();

            foreach ($this->selectedBreeds as $breed) {
                UserDogChoice::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'dog_breed' => $breed
                    ],
                    [] 
                );
            }

            DB::commit();

            $this->success('Success', 'Breeds saved successfully!');

            return redirect()->route('home'); // ❗ Use `return` here
        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('Error', 'An error occurred while saving your selection: '. $e->getMessage());

            // Optionally log the error
            // Log::error($e);

            return;
        }
    }
};
?>

<div>
    <x-header title="Dog Breeds" subtitle="Please select at least 3 breeds." separator>
        <x-slot:actions>
            <x-button
                icon="o-plus"
                :disabled="count($selectedBreeds) < 3"
                class="btn-primary"
                wire:click="saveSelection"
            >
                Save selection
            </x-button>
        </x-slot:actions>
    </x-header>

   <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @forelse ($this->breeds as $breed)
            <x-checkbox 
                :key="$breed['name']"
                :checked="in_array($breed['name'], $selectedBreeds)"
                wire:click="selectBreed('{{ $breed['name'] }}')"
                :disabled="count($selectedBreeds) >= 3 && !in_array($breed['name'], $selectedBreeds)"
                wire:target="selectBreed('{{ $breed['name'] }}')"  
                wire:loading.attr="disabled" 
            >
                <x-slot:label>
                    <div class="flex flex-col items-center w-full p-2 cursor-pointer rounded shadow transition duration-200">
                        <img src="{{ $breed['image'] }}" alt="{{ $breed['name'] }}" class="w-32 h-32 object-cover rounded-lg" loading="lazy">
                        <p class="mt-2 text-sm font-medium text-center">{{ $breed['name'] }}</p>
                    </div>
                </x-slot:label>
            </x-checkbox>
        @empty
            <p class="col-span-full text-center">No breeds loaded.</p>
        @endforelse
    </div>

    <div class="w-full flex justify-end my-4">
        <x-button
            icon="o-plus"
            :disabled="count($selectedBreeds) < 3"
            class="btn-primary"
            wire:click="saveSelection"
        >
            Save selection
        </x-button>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            (async () => {
                const breeds = await window.dog.fetchDogBreeds();
                Livewire.dispatch('breedsFetched', { breeds }); // no object wrapper
            })();
        });
    </script>
</div>
