<x-filament::page>
    <div class="space-y-6">


        <hr class="my-6 border-gray-300 dark:border-gray-700" />

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($images as $image)
                <div class="rounded-lg border shadow p-2 flex flex-col items-center">
                    <img src="{{ Storage::url($image->path) }}"
                         alt="{{ $image->title }}"
                         class="rounded w-full h-40 object-cover mb-2">

                    <h3 class="text-sm font-semibold">{{ $image->title }}</h3>
                    <p class="text-xs text-gray-500 mb-2">{{ $image->description }}</p>

                    <div class="flex gap-2">
                        <x-dynamic-component
                        :component="'filament::button'"
                        wire:click="toggleFavorite({{ $image->id }})"
                        :color="$image->is_favorite ? 'success' : 'gray'"
                        size="sm"
                        >
                        {{ $image->is_favorite ? 'Unfavorite' : 'Favorite' }}
                         </x-dynamic-component>


                        <x-filament::button
                            color="danger"
                            wire:click="removeImage({{ $image->id }})"
                            size="sm">
                            Delete
                        </x-filament::button>
                        <x-filament::button
                            color="primary"
                            wire:click="downloadImage('{{ $image->id }}')"
                            size="sm">
                            Download
                        </x-filament::button>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-500">
                    No images uploaded yet.
                </p>
            @endforelse
        </div>
        {{-- Upload Form --}}
        <form wire:submit.prevent="addImage" class="space-y-4 mb-6">
            {{ $this->form }}

            <x-filament::button type="submit" color="primary">
                Upload Image
            </x-filament::button>
        </form>
    </div>
    @include('partials.global-modal')

</x-filament::page>
