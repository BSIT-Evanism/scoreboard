<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileManagerResource\Pages;
use App\Models\FileManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Tables\Actions\ExportAction;

class FileManagerResource extends Resource
{
    protected static ?string $model = FileManager::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationLabel = 'File Manager';
    protected static ?string $modelLabel = 'File';
    protected static ?string $pluralModelLabel = 'Files';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File')
                            ->required()
                            ->directory('uploads')
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'image/*',
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/csv'
                            ])
                            ->maxSize(50 * 1024)  // 50MB max
                            ->visibility('public')
                            ->imagePreviewHeight('250')
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($state instanceof TemporaryUploadedFile) {
                                    $set('mime_type', $state->getMimeType());
                                    $set('size', $state->getSize());
                                    
                                    // If name is empty, use the file name
                                    if (empty($get('name'))) {
                                        $set('name', pathinfo($state->getClientOriginalName(), PATHINFO_FILENAME));
                                    }
                                }
                            }),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('mime_type'),
                        Forms\Components\Hidden::make('size'),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('mime_type_display')
                                    ->label('File Type')
                                    ->content(fn ($get) => $get('mime_type') ?: '-'),
                                Forms\Components\Placeholder::make('size_display')
                                    ->label('Size')
                                    ->content(function ($get) {
                                        $size = $get('size');
                                        return $size ? number_format($size / 1024, 2) . ' KB' : '-';
                                    }),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Preview')
                    ->square()
                    ->defaultImageUrl(function ($record) {
                        if (!$record->file_path) return null;
                        if (str_starts_with($record->mime_type, 'image/')) return null; // Let ImageColumn handle images
                        
                        // Return appropriate icon based on mime type
                        return match (true) {
                            str_contains($record->mime_type, 'pdf') => asset('images/pdf-icon.png'),
                            str_contains($record->mime_type, 'word') => asset('images/word-icon.png'),
                            str_contains($record->mime_type, 'excel') || str_contains($record->mime_type, 'spreadsheet') => asset('images/excel-icon.png'),
                            str_contains($record->mime_type, 'csv') => asset('images/csv-icon.png'),
                            default => asset('images/file-icon.png'),
                        };
                    }),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label('File Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalContent(fn (FileManager $record) => view(
                        'filament.resources.file-manager.preview',
                        ['record' => $record]
                    )),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (FileManager $record) {
                        if ($record->file_path) {
                            Storage::disk('public')->delete($record->file_path);
                        }
                    }),
                ExportAction::make()
                    ->exporter(YourExporter::class)
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            $records->each(function (FileManager $record) {
                                if ($record->file_path) {
                                    Storage::disk('public')->delete($record->file_path);
                                }
                            });
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFileManagers::route('/'),
            'create' => Pages\CreateFileManager::route('/create'),
            'edit' => Pages\EditFileManager::route('/{record}/edit'),
            'upload' => Pages\Upload::route('/upload'),
        ];
    }
}
