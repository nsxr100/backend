<?php

namespace App\Filament\Resources\MenuItems\Pages;

use App\Filament\Resources\MenuItems\MenuItemResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;

    protected function afterCreate(): void
    {
        $this->saveImageDataUrl();
    }

    private function saveImageDataUrl(): void
    {
        $path = $this->record->image_url;

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return;
        }

        $mime = Storage::disk('public')->mimeType($path) ?: 'image/jpeg';
        $data = base64_encode(Storage::disk('public')->get($path));

        $this->record->forceFill([
            'image_data_url' => "data:{$mime};base64,{$data}",
        ])->saveQuietly();
    }
}
