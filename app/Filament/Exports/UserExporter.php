<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name')
                ->label('Name'),
            ExportColumn::make('email')
                ->label('Email'),
            ExportColumn::make('role.name')
                ->label('Role')
                ->formatStateUsing(fn ($state) => $state ?? 'No Role'),
            ExportColumn::make('email_verified_at')
                ->label('Email Verified At')
                ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d H:i:s') : 'Not Verified'),
            ExportColumn::make('created_at')
                ->label('Created At')
                ->formatStateUsing(fn ($state) => $state->format('Y-m-d H:i:s')),
            ExportColumn::make('updated_at')
                ->label('Updated At')
                ->formatStateUsing(fn ($state) => $state->format('Y-m-d H:i:s')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function setUp(): void
    {
        $this->withFilename(fn () => 'users-' . now()->format('Y-m-d'))
            ->withChunkSize(100)
            ->queue();
    }
}
