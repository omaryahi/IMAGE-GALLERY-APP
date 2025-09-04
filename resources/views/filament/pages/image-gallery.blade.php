<x-filament-panels::page>
    <div class="space-y-6" x-data="imageModal">
        <!-- Search Bar -->
        <div class="mb-6 flex gap-4 ">
            <div class="flex-1">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        wire:model.live="search"
                        placeholder="{{ __('Search artworks...') }}"
                    />
                </x-filament::input.wrapper>
            </div>
            <x-filament::button wire:click="searchArtworks">
                {{ __('Search') }}
            </x-filament::button>
        </div>

        <!-- Loading -->
        <div wire:loading class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
            <p class="mt-2 text-gray-600">{{ __('Loading...') }}</p>
        </div>

        <!-- Images Grid -->
        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    @foreach($artworks as $artwork)
        <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col"
             wire:key="artwork-{{ $artwork['id'] }}">

            @if(!empty($artwork['image_url']))
                <img
                    src="{{ $artwork['thumbnail_url'] ?? $artwork['image_url'] }}"
                    alt="{{ $artwork['title'] ?? 'Untitled' }}"
                    class="w-full h-48 object-cover"
                    x-on:click="showImage({
                        url: '{{ $artwork['image_url'] }}',
                        title: '{{ addslashes($artwork['title'] ?? 'Untitled') }}',
                        artist: '{{ addslashes($artwork['artist_display'] ?? '') }}'
                    })"
                >
            @endif

            <div class="p-4 flex flex-col flex-grow">
                <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">
                    {{ $artwork['title'] ?? 'Untitled' }}
                </h3>
                @if(!empty($artwork['artist_display']))
                    <p class="text-sm text-gray-600 mb-4">
                        {{ $artwork['artist_display'] }}
                    </p>
                @endif

                <!-- Buttons Centered at Bottom -->
                <div class="mt-auto flex justify-center gap-2">
                    <x-filament::button
                        wire:click="toggleFavorite('{{ $artwork['id'] }}', {{ json_encode($artwork) }})"
                        :color="$this->isFavorited($artwork['id']) ? 'success' : 'gray'"
                        size="sm"
                    >
                        {{ $this->isFavorited($artwork['id']) ? 'Unfavorite' : 'Favorite' }}
                    </x-filament::button>

                    <x-filament::button
                        wire:click="downloadImage('{{ $artwork['id'] }}', '{{ addslashes($artwork['title'] ?? 'Untitled') }}', '{{ $artwork['type'] ?? 'api' }}')"
                        color="primary"
                        size="sm"
                    >
                        Download
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endforeach
</div>
        <!-- Pagination -->
        @if($totalPages > 1)
            <div class="flex justify-center items-center gap-2">
                <x-filament::button
                    wire:click="previousPage"
                    :disabled="$currentPage <= 1"
                    size="sm"
                >
                    {{ __('Previous') }}
                </x-filament::button>

                <span class="text-sm text-gray-600">
                    {{ __('Page :current of :total', ['current' => $currentPage, 'total' => $totalPages]) }}
                </span>

                <x-filament::button
                    wire:click="nextPage"
                    :disabled="$currentPage >= $totalPages"
                    size="sm"
                >
                    {{ __('Next') }}
                </x-filament::button>
            </div>
        @endif
    </div>
    @include('partials.global-modal')

</x-filament-panels::page>
