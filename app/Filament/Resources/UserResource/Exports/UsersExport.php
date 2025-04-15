<?php

namespace App\Filament\Resources\UserResource\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UsersExport extends Exporter
{
    protected static ?string $model = User::class;

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your users export has completed and is ready to download.';
    }

    public static function getFormSchema(): array
    {
        return [
            // Add any export options here if needed
        ];
    }

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

    protected function setUp(): void
    {
        $this->withFilename('users-' . now()->format('Y-m-d'))
            ->withChunkSize(100)
            ->queue();
    }

    public function styles($sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2E8F0',
                ],
            ],
        ]);

        // Auto-size columns
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
} 