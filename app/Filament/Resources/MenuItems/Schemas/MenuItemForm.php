<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('base_price')
                    ->required()
                    ->numeric()
                    ->prefix('₱'),
                FileUpload::make('image_url')
                    ->label('Menu image')
                    ->image()
                    ->disk('public')
                    ->directory('menu-items')
                    ->visibility('public')
                    ->imageEditor()
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, Set $set, FileUpload $component): ?string {
                        $path = $component->saveUploadedFile($file);

                        if (! $path || ! Storage::disk('public')->exists($path)) {
                            return $path;
                        }

                        $mime = Storage::disk('public')->mimeType($path) ?: 'image/jpeg';
                        $data = base64_encode(Storage::disk('public')->get($path));

                        $set('image_data_url', "data:{$mime};base64,{$data}");

                        return $path;
                    })
                    ->columnSpanFull(),
                Hidden::make('image_data_url'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
