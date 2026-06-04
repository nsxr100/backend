<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'base_price',
        'image_url',
        'image_data_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'image_data_url',
    ];

    protected static function booted(): void
    {
        static::saved(function (MenuItem $menuItem): void {
            if (! $menuItem->image_url) {
                return;
            }

            if ($menuItem->image_data_url && ! $menuItem->wasChanged('image_url')) {
                return;
            }

            if (! Storage::disk('public')->exists($menuItem->image_url)) {
                return;
            }

            $mime = Storage::disk('public')->mimeType($menuItem->image_url) ?: 'image/jpeg';
            $data = base64_encode(Storage::disk('public')->get($menuItem->image_url));

            $menuItem->forceFill([
                'image_data_url' => "data:{$mime};base64,{$data}",
            ])->saveQuietly();
        });
    }

    /**
     * Get the category of the menu item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the variants of the menu item.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(MenuVariant::class);
    }
}
