<?php

namespace App\Filament\Resources\FileManagerResource\Pages;

use App\Filament\Resources\FileManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFileManager extends EditRecord
{
    protected static string $resource = FileManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
