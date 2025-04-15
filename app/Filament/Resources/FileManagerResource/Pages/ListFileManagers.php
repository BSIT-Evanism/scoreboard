<?php

namespace App\Filament\Resources\FileManagerResource\Pages;

use App\Filament\Resources\FileManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFileManagers extends ListRecords
{
    protected static string $resource = FileManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upload')
                ->url(static::$resource::getUrl('upload'))
                ->icon('heroicon-m-cloud-arrow-up')
                ->color('primary'),
            Actions\CreateAction::make(),
        ];
    }
}
