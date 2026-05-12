<?php

namespace App\Models;

use App\Services\EngineSchemaService;
use Illuminate\Database\Eloquent\Model;
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

        // ── DDL lifecycle: each object type owns its own real table.
        static::created(function (self $type) {
            app(EngineSchemaService::class)->create($type);
        });

        static::updated(function (self $type) {
            if ($type->wasChanged('attributes')) {
                $previous = (array) ($type->getOriginal('attributes') ? json_decode($type->getOriginal('attributes'), true) : []);
                app(EngineSchemaService::class)->syncColumns($type, $previous);
            }
        });

        static::deleting(function (self $type) {
            app(EngineSchemaService::class)->drop($type);
        });
    }

    public function engineTable(): string
    {
        return app(EngineSchemaService::class)->tableFor($this);
    }
}
