<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\Setting;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;

class Estructura extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static string $view = 'filament.pages.estructura';

    protected static ?string $navigationLabel = 'Estructura';

    protected static ?string $title = 'Estructura del Sitio';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Header')
                            ->schema([
                                Section::make('Cabecera')
                                    ->schema([
                                        FileUpload::make('header_logo')
                                            ->label('Logo')
                                            ->image()
                                            ->disk('public')
                                            ->directory('settings')
                                            ->preserveFilenames(),
                                        ColorPicker::make('header_bg_color')
                                            ->label('Color de Fondo'),
                                        ColorPicker::make('header_text_color')
                                            ->label('Color de Texto'),
                                    ])->columns(2),
                            ]),
                        Tabs\Tab::make('Footer')
                            ->schema([
                                Section::make('Pie de Página')
                                    ->schema([
                                        RichEditor::make('footer_content')
                                            ->label('Contenido del Pie de Página')
                                            ->columnSpanFull(),
                                        ColorPicker::make('footer_bg_color')
                                            ->label('Color de Fondo'),
                                        ColorPicker::make('footer_text_color')
                                            ->label('Color de Texto'),
                                    ])->columns(2),
                            ]),
                        Tabs\Tab::make('Estilos Generales')
                            ->schema([
                                Section::make('Botones y Fondo')
                                    ->schema([
                                        ColorPicker::make('primary_button_color')
                                            ->label('Color Botón Primario'),
                                        ColorPicker::make('secondary_button_color')
                                            ->label('Color Botón Secundario'),
                                        ColorPicker::make('body_bg_color')
                                            ->label('Color de Fondo de la Página'),
                                    ])->columns(3),
                            ]),
                        Tabs\Tab::make('Menú Principal')
                            ->schema([
                                Section::make('Estilos del Menú')
                                    ->description('Estilos para el menú principal que se encuentra debajo del footer')
                                    ->schema([
                                        ColorPicker::make('menu_bg_color')
                                            ->label('Color de Fondo'),
                                        ColorPicker::make('menu_text_color')
                                            ->label('Color de Texto'),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // Handle file upload array if needed, but usually single file upload returns string path.
            // If value is null, keep it null.
            if (is_array($value) && isset($value[0]) && count($value) === 1) {
                // Sometimes file upload returns array even for single file?
                // No, Filament Image stores string path by default if not multiple.
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}
