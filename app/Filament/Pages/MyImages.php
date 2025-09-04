<?php

namespace App\Filament\Pages;

use App\Models\UserImage;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageDownloadService;

class MyImages extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static string $view = 'filament.pages.my-images';
    protected static ?string $navigationLabel = 'My Images';
    protected static ?int $navigationSort = 2;

    public $images = [];
    public $data = []; // form state lives here
    protected $downloadService;

    public function mount(): void
    {
        $this->form->fill();
        $this->downloadService = new ImageDownloadService();
        $this->loadImages();
    }

    public function loadImages()
    {
        $this->images = UserImage::orderBy('created_at', 'desc')->get();
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description'),
                Forms\Components\FileUpload::make('image')
                    ->disk('public')
                    ->directory('user-images')
                    ->image()
                    ->required(),
            ])
            ->statePath('data'); // bind to $data
    }

    public function addImage()
    {
        $data = $this->form->getState(); // get form data

        if (!empty($data['image'])) {
            $path = $data['image'];
            $filename = basename($path);

            UserImage::create([
                'title'       => $data['title'],
                'description' => $data['description'],
                'filename'    => $filename,
                'path'        => $path,
                'size'        => Storage::disk('public')->size($path),
                'mime_type'   => Storage::disk('public')->mimeType($path),
                'is_favorite' => false, // default
            ]);

            $this->form->fill(); // reset form
            $this->loadImages();

            $this->dispatch('image-added');
        }
    }

    public function removeImage($imageId)
    {
        $image = UserImage::find($imageId);

        if ($image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
            $this->loadImages();
        }
    }

    public function toggleFavorite($id)
    {
        $image = UserImage::find($id);

        if ($image) {
            $image->is_favorite = ! $image->is_favorite;
            $image->save();
            $this->loadImages();
        }
    }

    public function downloadImage($id)
{
    $userImage = UserImage::find($id);

        if (!$userImage) {
            abort(404, 'Image not found in database.');
        }

        // storage/app/public/ + path from DB
        $path = storage_path('app/public/' . $userImage->path);

        if (!file_exists($path)) {
            abort(404, 'Image file missing.');
        }
        $filename = basename($path);

        // keep correct file extension
        $extension = pathinfo($userImage->filename, PATHINFO_EXTENSION);

        return response()->download(
            $path,
            $filename . '.' . $extension
        );
}

    public static function getNavigationLabel(): string
    {
        return __('My Images');
    }
}
