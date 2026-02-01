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
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Vista Previa')
                    ->square()
                    ->visibility('public'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL Pública')
                    ->getStateUsing(fn(MediaFile $record) => $record->url)
                    ->copyable()
                    ->copyMessage('URL copiada al portapapeles')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-link'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
