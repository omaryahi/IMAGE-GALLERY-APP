<x-filament-panels::page>
    <div x-data="{
        selectedImage: null,
        showModal: false,
        showImage(image) {
            this.selectedImage = image;
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.selectedImage = null;
        }
    }" class="space-y-6">
        <!-- Search Bar -->
        <div class="mb-6 flex gap-4">
            <div class="flex-1">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        wire:model.live="search"
                        placeholder="{{ __('Search favorites...') }}"
                    />
                </x-filament::input.wrapper>
            </div>
            <x-filament::button wire:click="searchFavorites">{{ __('Search') }}</x-filament::button>
        </div>

        <!-- Loading State -->
        <div wire:loading class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
            <p class="mt-2 text-gray-600">{{ __('Loading...') }}</p>
        </div>

        <!-- Favorites Grid -->
        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
            @foreach($favorites as $favorite)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative group">
                        <img
                            src="{{ $favorite['data']['thumbnail_url'] }}"
                            alt="{{ $favorite['data']['title'] }}"
                            class="w-full h-48 object-cover cursor-pointer"
                            x-on:click="showImage({
                                url: '{{ $favorite['data']['image_url'] }}',
                                title: '{{ addslashes($favorite['data']['title']) }}',
                                artist: '{{ addslashes($favorite['data']['artist_display'] ?? $favorite['data']['description'] ?? '') }}'
                            })"
                        >


                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $favorite['data']['title'] }}</h3>
                        @if(isset($favorite['data']['artist_display']))
                            <p class="text-sm text-gray-600 line-clamp-1">{{ $favorite['data']['artist_display'] }}</p>
                        @elseif(isset($favorite['data']['description']))
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $favorite['data']['description'] }}</p>
                        @endif
                    </div>
                                            <div class="mt-auto flex justify-center gap-2">
                            <x-filament::button
                                wire:click="removeFavorite({{ $favorite['id'] }})"
                                :color="$this->isFavorited($favorite['id']) ? 'success' : 'gray'"
                                size="sm"
                            >
                                {{ $this->isFavorited($favorite['id']) ? 'Unfavorite' : 'Favorite' }}
                            </x-filament::button>

                            <x-filament::button
                                wire:click="downloadFavorite('{{ $favorite['data']['image_id'] }}', '{{ $favorite['data']['title'] }}', '{{ $favorite['type'] }}')"
                                color="primary"
                                size="sm"
                            >
                                Download
                            </x-filament::button>
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

        @if(count($favorites) === 0)
            <div class="text-center py-8">
                <x-heroicon-o-heart class="w-12 h-12 text-gray-400 mx-auto mb-4"/>
                <p class="text-gray-500">{{ __('No favorites found') }}</p>
            </div>
        @endif

        <!-- Image Modal -->
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
             @click="closeModal()"
             x-cloak>
            <div class="max-w-4xl max-h-full relative">
                <img x-show="selectedImage"
                     :src="selectedImage?.url"
                     :alt="selectedImage?.title"
                     class="max-w-full max-h-full object-contain rounded-lg"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:enter-end="opacity-100 scale-100">
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-4 rounded-b-lg">
                    <h3 class="text-white font-semibold" x-text="selectedImage?.title"></h3>
                    <p class="text-gray-300 text-sm" x-text="selectedImage?.artist || selectedImage?.description"></p>
                </div>
                <button @click="closeModal()"
                        class="absolute top-2 right-2 text-white hover:text-gray-300 transition-colors">
                    <x-heroicon-s-x-mark class="w-6 h-6"/>
                </button>
            </div>
        </div>
    </div>
    @include('partials.global-modal')

</x-filament-panels::page>
