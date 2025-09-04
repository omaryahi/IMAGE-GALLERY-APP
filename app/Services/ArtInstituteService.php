<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ArtInstituteService
{
    private $baseUrl = 'https://api.artic.edu/api/v1';
    private $imageBaseUrl = 'https://www.artic.edu/iiif/2';

    public function getArtworks($page = 1, $limit = 12, $search = null)
    {
        $cacheKey = "artworks_page_{$page}_limit_{$limit}_search_" . md5($search ?? '');

        return Cache::remember($cacheKey, 300, function () use ($page, $limit, $search) {
            $params = [
                'page' => $page,
                'limit' => $limit,
                'fields' => 'id,title,artist_display,date_display,image_id,thumbnail'
            ];

            $url = $this->baseUrl . '/artworks';

            if ($search) {
                $url = $this->baseUrl . '/artworks/search';
                $params['q'] = $search;
            }

            $response = Http::get($url, $params);

            if ($response->successful()) {
                $data = $response->json();

                // Process images to add full URLs
                if (isset($data['data'])) {
                    $data['data'] = collect($data['data'])->map(function ($artwork) {
                        if ($artwork['image_id']) {
                            $artwork['image_url'] = $this->getImageUrl($artwork['image_id']);
                            $artwork['thumbnail_url'] = $this->getThumbnailUrl($artwork['image_id']);
                        }
                        return $artwork;
                    })->toArray();
                }

                return $data;
            }

            return null;
        });
    }

    public function getImageUrl($imageId, $size = '843,')
    {
        return "{$this->imageBaseUrl}/{$imageId}/full/{$size}/0/default.jpg";
    }

    public function getThumbnailUrl($imageId)
    {
        return "{$this->imageBaseUrl}/{$imageId}/full/400,/0/default.jpg";
    }

    public function downloadImage($imageId)
    {
        $url = $this->getImageUrl($imageId);
        return Http::get($url);
    }
}
