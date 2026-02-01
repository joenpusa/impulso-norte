<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PageResource extends Resource
{
    protected static ?string $modelLabel = 'Página';
    protected static ?string $pluralModelLabel = 'Páginas';
    protected static ?string $navigationLabel = 'Páginas';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(string $state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Slug / URL')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),
                                    ]),
                                Forms\Components\Builder::make('content')
                                    ->label('Contenido')
                                    ->blocks([
                                        Forms\Components\Builder\Block::make('hero')
                                            ->label('Sección Hero (Banner)')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')->label('Título')->required(),
                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Imagen de Fondo')
                                                    ->image()
                                                    ->maxSize(2048)
                                                    ->directory('hero')
                                                    ->required(),
                                                Forms\Components\TextInput::make('cta_text')->label('Texto del Botón'),
                                                Forms\Components\TextInput::make('cta_url')->label('Enlace del Botón'),
                                            ]),
                                        Forms\Components\Builder\Block::make('text')
                                            ->label('Bloque de Texto')
                                            ->schema([
                                                Forms\Components\RichEditor::make('content')
                                                    ->label('Contenido')
                                                    ->required(),
                                            ]),
                                        Forms\Components\Builder\Block::make('image')
                                            ->label('Imagen Simple')
                                            ->schema([
                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Imagen')
                                                    ->image()
                                                    ->maxSize(2048)
                                                    ->directory('content')
                                                    ->required(),
                                                Forms\Components\TextInput::make('caption')->label('Pie de foto'),
                                            ]),
                                        Forms\Components\Builder\Block::make('gallery')
                                            ->label('Galería de Imágenes')
                                            ->schema([
                                                Forms\Components\Repeater::make('images')
                                                    ->label('Imágenes')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')
                                                            ->label('Imagen')
                                                            ->image()
                                                            ->maxSize(2048)
                                                            ->directory('gallery')
                                                            ->required(),
                                                    ]),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(['lg' => 2]),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Configuración')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_published')
                                            ->label('Publicado')
                                            ->required(),
                                        Forms\Components\Toggle::make('is_home')
                                            ->label('Es Página de Inicio')
                                            ->required(),
                                    ]),
                                Forms\Components\Section::make('SEO')
                                    ->schema([
                                        Forms\Components\TextInput::make('seo_title')
                                            ->label('Título SEO')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('seo_description')
                                            ->label('Descripción SEO')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publicado')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_home')
                    ->label('Inicio')
                    ->boolean(),
                Tables\Columns\TextColumn::make('seo_title')
                    ->label('Título SEO')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
