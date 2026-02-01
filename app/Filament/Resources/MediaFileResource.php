<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaFileResource\Pages;
use App\Filament\Resources\MediaFileResource\RelationManagers;
use App\Models\MediaFile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MediaFileResource extends Resource
{
    protected static ?string $modelLabel = 'Archivo';
    protected static ?string $pluralModelLabel = 'Multimedia';
    protected static ?string $navigationLabel = 'Multimedia';
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Cargar Archivo')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título / Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('path')
                            ->label('Archivo (Imagen o PDF)')
                            ->required()
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->directory('multimedia/' . date('Y-m-d'))
                            ->preserveFilenames()
                            ->storeFileNamesIn('title')
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, Forms\Set $set, $component) {
                                // Logic to capture type/size could go here if needed, 
                                // but FileUpload handles storage. 
                                // We can use saving hooks or rely on basic metadata.
                                if ($file = $component->getState()) {
                                    // $set('type', $file->getMimeType());
                                }
                            }),
                        Forms\Components\Hidden::make('type'),
                        Forms\Components\Hidden::make('size'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->recordUrl(null) // Disable click to edit
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('path')
                        ->label('Imagen')
                        ->height('200px')
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'object-cover w-full h-full rounded-lg']),

                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('title')
                            ->label('Nombre')
                            ->weight('bold')
                            ->searchable()
                            ->sortable(),
                    ])->space(1),
                ])->space(3),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('copy_url')
                    ->label('Copiar URL')
                    ->icon('heroicon-o-clipboard')
                    ->iconButton()
                    ->color('primary')
                    ->action(function () {}) // No server-side action needed, purely client-side copy
                    ->extraAttributes(function (MediaFile $record) {
                        return [
                            'x-on:click' => 'window.navigator.clipboard.writeText("' . $record->url . '"); $tooltip("URL copiada al portapapeles", { timeout: 1500 });',
                            'title' => 'Copiar URL',
                        ];
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->modalHeading('¿Eliminar archivo?')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este archivo? Esta acción no se puede deshacer y el archivo se borrará del servidor.')
                    ->modalSubmitActionLabel('Sí, eliminar')
                    ->modalCancelActionLabel('Cancelar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->modalHeading('¿Eliminar archivos seleccionados?')
                        ->modalDescription('¿Estás seguro de que deseas eliminar los archivos seleccionados? Se borrarán permanentemente del servidor.')
                        ->modalSubmitActionLabel('Sí, eliminar todos')
                        ->modalCancelActionLabel('Cancelar'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No hay archivos multimedia')
            ->emptyStateDescription('Sube un archivo para comenzar.');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaFiles::route('/'),
            'create' => Pages\CreateMediaFile::route('/create'),
            'edit' => Pages\EditMediaFile::route('/{record}/edit'),
        ];
    }
}
