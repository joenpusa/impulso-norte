<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuResource extends Resource
{
    protected static ?string $modelLabel = 'Menú';
    protected static ?string $pluralModelLabel = 'Menús';
    protected static ?string $navigationLabel = 'Menús';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Menú')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('location')
                            ->label('Ubicación')
                            ->options([
                                'header' => 'Menú Principal',
                                'footer' => 'Pie de Página',
                            ]),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->required(),
                    ]),
                Forms\Components\Section::make('Menu Items')
                    ->heading('Elementos del Menú')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Elementos')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título')
                                    ->required(),
                                Forms\Components\TextInput::make('url')
                                    ->label('URL Externa')
                                    ->prefix('URL'),
                                Forms\Components\Select::make('page_id')
                                    ->label('O Enlazar a Página')
                                    ->relationship('page', 'title')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Select::make('target')
                                    ->label('Abrir en')
                                    ->options([
                                        '_self' => 'Misma Pestaña',
                                        '_blank' => 'Nueva Pestaña',
                                    ])
                                    ->default('_self')
                                    ->required(),
                                Forms\Components\Repeater::make('children')
                                    ->label('Sub-elementos')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required(),
                                        Forms\Components\TextInput::make('url')
                                            ->label('URL Externa'),
                                        Forms\Components\Select::make('page_id')
                                            ->label('O Enlazar a Página')
                                            ->relationship('page', 'title')
                                            ->searchable()
                                            ->preload(),
                                        Forms\Components\Select::make('target')
                                            ->label('Abrir en')
                                            ->options([
                                                '_self' => 'Misma Pestaña',
                                                '_blank' => 'Nueva Pestaña',
                                            ])
                                            ->default('_self')
                                            ->required(),
                                    ])
                                    ->columnSpanFull()
                                    ->collapsed()
                                    ->orderColumn('order'),
                            ])
                            ->orderColumn('order')
                            ->collapsed()
                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
