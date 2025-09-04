<?php

namespace App\Filament\Pages;

use App\Models\Favorite;
use App\Models\UserImage;
use App\Services\ArtInstituteService;
use App\Services\ImageDownloadService;
use Filament\Pages\Page;

class Favorites extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static string $view = 'filament.pages.favorites';
    protected static ?string $navigationLabel = 'Favorites';
    protected static ?int $navigationSort = 3;

    public $favorites = [];
    public $search = '';
    public $currentPage = 1;
    public $perPage = 12;
    public $totalPages = 1;
    public $loading = false;

    protected $artService;
    protected $downloadService;

    public function boot()
    {
        $this->artService = new ArtInstituteService();
        $this->downloadService = new ImageDownloadService();

        $this->loadFavorites();
    }

    public function loadFavorites()
    {
        $this->loading = true;

        // === API favorites ===
        $query = Favorite::query();
        if ($this->search) {
            $query->where('image_data->title', 'like', '%' . $this->search . '%');
        }

        $paginator = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage, ['*'], 'page', $this->currentPage);

        $apiFavorites = collect($paginator->items())->map(function ($favorite) {
            $data = $favorite->image_data;

            if ($favorite->image_type === 'api' && isset($data['image_id'])) {
                $data['image_url'] = $this->artService->getImageUrl($data['image_id']);
                $data['thumbnail_url'] = $this->artService->getThumbnailUrl($data['image_id']);
            }

            return [
                'id' => $favorite->id,
                'type' => $favorite->image_type,
                'image_id' => $data['image_id'],
                'data' => $data,
            ];
        });

        // === User uploaded favorites ===
        $userFavorites = UserImage::where('is_favorite', true)
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'type' => 'user_upload',
                    'image_id' => $image->id,
                    'data' => [
                        'title' => $image->title,
                        'description' => $image->description,
                        'image_url' => \Storage::url($image->path),
                        'thumbnail_url' => \Storage::url($image->path),
                    ],
                ];
            });

        $this->favorites = $apiFavorites->merge($userFavorites)->toArray();

        // Pagination info
        $this->currentPage = $paginator->currentPage();
        $this->totalPages  = $paginator->lastPage();

        $this->loading = false;
    }

    public function searchFavorites()
    {
        $this->currentPage = 1;
        $this->loadFavorites();
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->loadFavorites();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadFavorites();
        }
    }

    public function removeFavorite($favoriteId)
    {
        Favorite::find($favoriteId)?->delete();
        UserImage::where('id', $favoriteId)->update(['is_favorite' => false]);

        $this->loadFavorites();
    }

    public function downloadFavorite($imageId, $title, $type = 'api')
    {
        $image = [
            'type' => $type,
            'image_id' => $imageId,
            'title' => $title,
        ];

        return $this->downloadService->download($image);
    }

    public function isFavorited($favoriteId)
    {
        return Favorite::where('id', $favoriteId)->exists() || UserImage::where('id', $favoriteId)->where('is_favorite', true)->exists();
    }

    public static function getNavigationLabel(): string
    {
        return __('Favorites');
    }
}
