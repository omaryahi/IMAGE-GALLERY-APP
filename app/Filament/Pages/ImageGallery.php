<?php

namespace App\Filament\Pages;

use App\Models\Favorite;
use App\Services\ArtInstituteService;
use App\Services\ImageDownloadService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Filament\Notifications\Notification;

class ImageGallery extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static string $view = 'filament.pages.image-gallery';
    protected static ?string $navigationLabel = 'Image Gallery';
    protected static ?int $navigationSort = 1;

    public $artworks = [];
    public $currentPage = 1;
    public $totalPages = 1;
    public $search = '';

    protected ?ArtInstituteService $artService=null;
    protected $downloadService;

    public function mount()
    {
        $this->downloadService = new ImageDownloadService();
        $this->artService = new ArtInstituteService();
        $this->loadArtworks();
    }

public function loadArtworks()
{
    $page = $this->currentPage;
    $needed = 12;
    if (!$this->artService) {
        $this->artService = new ArtInstituteService();
    }
    $this->artworks = [];

    while (count($this->artworks) < $needed) {
        $response = $this->artService->getArtworks(
            $page,
            $needed,
            $this->search ?: null
        );

        if (!$response || empty($response['data'])) {
            break; // stop if API has no more results
        }

        $filtered = collect($response['data'])
            ->filter(fn($artwork) => !empty($artwork['image_url']))
            ->map(function ($artwork) {
                return [
                    'id' => $artwork['id'] ?? null,
                    'title' => $artwork['title'] ?? '',
                    'artist_display' => $artwork['artist_display'] ?? '',
                    'thumbnail_url' => $artwork['thumbnail_url'] ?? asset('images/placeholder.png'),
                    'image_url' => $artwork['image_url'] ?? null,
                    'image_id' => $artwork['image_id'] ?? null,
                ];
            })
            ->toArray();

        $this->artworks = array_merge($this->artworks, $filtered);

        $page++;
    }

    $this->artworks = array_slice($this->artworks, 0, $needed);

    $this->totalPages = $response['pagination']['total_pages'] ?? 1;
}





public function searchArtworks()
{
    $this->currentPage = 1;
    $this->artworks = []; // clear old results
    $service = new ArtInstituteService();


    $response = $service->getArtworks(
        $this->currentPage,
        1,
        $this->search
    );

    if ($response) {
        $this->artworks = collect($response['data'] ?? [])->map(function ($artwork) {
            return [
                'id' => $artwork['id'] ?? null,
                'title' => $artwork['title'] ?? '',
                'artist_display' => $artwork['artist_display'] ?? '',
                'thumbnail_url' => $artwork['thumbnail_url'] ?? asset('images/placeholder.png'),
                'image_url' => $artwork['image_url'] ?? null,
                'image_id' => $artwork['image_id'] ?? null,
            ];
        })->toArray();

        $this->totalPages = $response['pagination']['total_pages'] ?? 1;
    }
}


    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadArtworks();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadArtworks();
        }
    }


    public function toggleFavorite($artworkId, $artworkData)
    {
        $favorite = Favorite::where('image_type', 'api')
            ->where('image_id', $artworkId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            Notification::make()
                ->title('Removed from favorites!')
                ->warning()
                ->send();
        } else {
            Favorite::create([
                'image_type' => 'api',
                'image_id' => $artworkId,
                'image_data' => $artworkData
            ]);
            Notification::make()
                ->title('Added to favorites!')
                ->success()
                ->send();
        }
    }

    public function downloadImage($imageId, $title, $type = 'api')
    {
        // Find the artwork
        $artwork = collect($this->artworks)->firstWhere('id', $imageId);

        if (!$artwork || empty($artwork['image_url'])) {
            $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Image not found!'
            ]);
            return;
        }

        $url = $artwork['image_url'];

        // Stream the download
        return response()->streamDownload(function() use ($url) {
            echo file_get_contents($url);
        }, $title . '.jpg');
    }

    public function isFavorited($artworkId)
    {
        return Favorite::where('image_type', 'api')
            ->where('image_id', $artworkId)
            ->exists();
    }

    public static function getNavigationLabel(): string
    {
        return __('Image Gallery');
    }
}
