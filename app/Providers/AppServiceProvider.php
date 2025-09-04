<?php

namespace App\Providers;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportTesting\Testable;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         FilamentAsset::register([
            Css::make('custom-stylesheet', resource_path('css/custom.css')),
            Js::make('custom-script', resource_path('js/custom.js')),
        ]);
          FilamentColor::register([
            'danger' => '#ef4444',
            'gray' => '#6b7280',
            'info' => '#3b82f6',
            'primary' => '#6366f1',
            'success' => '#10b981',
            'warning' => '#f59e0b',
        ]);
        FilamentIcon::register([
            'actions::create-action' => 'heroicon-m-plus',
            'actions::delete-action' => 'heroicon-m-trash',
            'actions::edit-action' => 'heroicon-m-pencil-square',
            'actions::view-action' => 'heroicon-m-eye',
        ]);
        
    }
}
