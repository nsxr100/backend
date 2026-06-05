<?php

namespace App\Filament\Resources\MenuVariants\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuVariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('menu_item_id')
                    ->relationship('menuItem', 'name')
                    ->required(),
                TextInput::make('name')
                    ->autocomplete(false)
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->autocomplete(false)
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
