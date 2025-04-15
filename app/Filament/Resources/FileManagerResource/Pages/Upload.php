<?php

namespace App\Filament\Resources\FileManagerResource\Pages;

use App\Filament\Resources\FileManagerResource;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;

class Upload extends Page
{
    protected static string $resource = FileManagerResource::class;

    protected static string $view = 'filament.resources.file-manager.upload';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->url(FileManagerResource::getUrl())
                ->color('gray'),
        ];
    }
} 