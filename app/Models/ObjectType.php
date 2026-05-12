<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ObjectType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'attributes',
        'sort_order',
    ];

    public const ICON_CHOICES = [
        'heroicon-o-cube'                  => '⬜ Cube',
        'heroicon-o-device-phone-mobile'   => '📱 Phone',
        'heroicon-o-device-tablet'         => '📱 Tablet',
        'heroicon-o-computer-desktop'      => '🖥 Computer',
        'heroicon-o-tv'                    => '📺 TV',
        'heroicon-o-camera'                => '📷 Camera',
        'heroicon-o-cpu-chip'              => '🔲 Chip',
        'heroicon-o-bolt'                  => '⚡ Bolt / Power',
        'heroicon-o-wrench'                => '🔧 Wrench',
        'heroicon-o-wrench-screwdriver'    => '🛠 Tools',
        'heroicon-o-cog-6-tooth'           => '⚙ Settings',
        'heroicon-o-archive-box'           => '📦 Box',
        'heroicon-o-shopping-bag'          => '🛍 Shopping bag',
        'heroicon-o-tag'                   => '🏷 Tag',
        'heroicon-o-currency-dollar'       => '💲 Currency',
        'heroicon-o-credit-card'           => '💳 Credit card',
        'heroicon-o-truck'                 => '🚚 Truck',
        'heroicon-o-key'                   => '🗝 Key',
        'heroicon-o-document-text'         => '📄 Document',
        'heroicon-o-clipboard-document'    => '📋 Clipboard',
        'heroicon-o-photo'                 => '🖼 Photo',
        'heroicon-o-book-open'             => '📖 Book',
        'heroicon-o-gift'                  => '🎁 Gift',
        'heroicon-o-puzzle-piece'          => '🧩 Puzzle',
        'heroicon-o-musical-note'          => '🎵 Music',
        'heroicon-o-rocket-launch'         => '🚀 Rocket',
        'heroicon-o-shield-check'          => '🛡 Shield',
        'heroicon-o-sparkles'              => '✨ Sparkles',
        'heroicon-o-user-group'            => '👥 People',
        'heroicon-o-building-storefront'   => '🏪 Storefront',
    ];

    protected $casts = [
        'attributes' => 'array',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $type) {
            if (blank($type->slug) && filled($type->name)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    public function records(): HasMany
    {
        return $this->hasMany(ObjectRecord::class);
    }
}
