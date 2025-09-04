<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\UserImage;

class ImageDownloadService
{
    protected $artService;

    public function __construct()
    {
        $this->artService = new ArtInstituteService(); // For API images
    }

    /**
     * Download an image based on its type
     *
     * @param array $image ['type' => 'api'|'user', 'image_id' => '...']
     */

public function download(array $image)
{
    $filename = $image['title'] ?? 'downloaded_image';

    // ---- API Images ----
    if ($image['type'] === 'api') {
        \Log::info('Downloading API image', $image);
        $response = $this->artService->downloadImage($image['image_id']);
        if (! $response->successful()) {
        \Log::error('API download failed', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
    }

        return response()->streamDownload(function () use ($response) {
            echo $response->body();
        }, $filename . '.jpg', [
            'Content-Type' => $response->header('Content-Type') ?? 'image/jpeg',
        ]);
    }

    // ---- User Uploads ----
    if ($image['type'] === 'user_upload') {
        $userImage = UserImage::find($image['image_id']);

        if (!$userImage) {
            abort(404, 'Image not found in database.');
        }

        // storage/app/public/ + path from DB
        $path = storage_path('app/public/' . $userImage->path);

        if (!file_exists($path)) {
            abort(404, 'Image file missing.');
        }

        // keep correct file extension
        $extension = pathinfo($userImage->filename, PATHINFO_EXTENSION);

        return response()->download(
            $path,
            $filename . '.' . $extension
        );
    }

    // ---- Unknown type ----
    abort(400, 'Unknown image type.');
}


}
