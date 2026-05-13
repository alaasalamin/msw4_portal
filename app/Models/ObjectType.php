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
        'contract_template',
        'sort_order',
    ];

    public const ICON_CHOICES = [
        // ── Shapes / generic
        'heroicon-o-cube'                  => 'Cube',
        'heroicon-o-square-3-stack-3d'     => 'Stack',
        'heroicon-o-rectangle-stack'       => 'Cards stack',
        'heroicon-o-squares-2x2'           => 'Grid',
        'heroicon-o-squares-plus'          => 'Grid plus',
        'heroicon-o-puzzle-piece'          => 'Puzzle',
        'heroicon-o-sparkles'              => 'Sparkles',
        'heroicon-o-star'                  => 'Star',
        'heroicon-o-fire'                  => 'Fire',
        'heroicon-o-bolt'                  => 'Bolt',
        'heroicon-o-light-bulb'            => 'Lightbulb',
        'heroicon-o-flag'                  => 'Flag',
        'heroicon-o-trophy'                => 'Trophy',
        'heroicon-o-heart'                 => 'Heart',
        'heroicon-o-check-badge'           => 'Verified',
        'heroicon-o-academic-cap'          => 'Academic cap',

        // ── Devices / electronics
        'heroicon-o-device-phone-mobile'   => 'Phone',
        'heroicon-o-device-tablet'         => 'Tablet',
        'heroicon-o-computer-desktop'      => 'Computer',
        'heroicon-o-tv'                    => 'TV',
        'heroicon-o-camera'                => 'Camera',
        'heroicon-o-cpu-chip'              => 'Chip',
        'heroicon-o-server'                => 'Server',
        'heroicon-o-server-stack'          => 'Server stack',
        'heroicon-o-radio'                 => 'Radio',
        'heroicon-o-printer'               => 'Printer',
        'heroicon-o-battery-100'           => 'Battery',
        'heroicon-o-signal'                => 'Signal',
        'heroicon-o-wifi'                  => 'Wi-Fi',

        // ── Tools / settings
        'heroicon-o-wrench'                => 'Wrench',
        'heroicon-o-wrench-screwdriver'    => 'Tools',
        'heroicon-o-cog-6-tooth'           => 'Settings',
        'heroicon-o-adjustments-horizontal' => 'Adjustments',
        'heroicon-o-bug-ant'               => 'Bug',
        'heroicon-o-beaker'                => 'Beaker',

        // ── Storage / packaging / commerce
        'heroicon-o-archive-box'           => 'Box',
        'heroicon-o-archive-box-arrow-down' => 'Box in',
        'heroicon-o-folder'                => 'Folder',
        'heroicon-o-folder-open'           => 'Folder open',
        'heroicon-o-inbox'                 => 'Inbox',
        'heroicon-o-inbox-stack'           => 'Inbox stack',
        'heroicon-o-shopping-bag'          => 'Shopping bag',
        'heroicon-o-shopping-cart'         => 'Shopping cart',
        'heroicon-o-receipt-percent'       => 'Receipt',
        'heroicon-o-tag'                   => 'Tag',
        'heroicon-o-ticket'                => 'Ticket',
        'heroicon-o-banknotes'             => 'Banknotes',
        'heroicon-o-currency-dollar'       => 'Currency',
        'heroicon-o-currency-euro'         => 'Euro',
        'heroicon-o-credit-card'           => 'Credit card',
        'heroicon-o-truck'                 => 'Truck',
        'heroicon-o-rocket-launch'         => 'Rocket',
        'heroicon-o-gift'                  => 'Gift',

        // ── Documents / files
        'heroicon-o-document'              => 'Document',
        'heroicon-o-document-text'         => 'Document text',
        'heroicon-o-document-duplicate'    => 'Document duplicate',
        'heroicon-o-document-check'        => 'Document check',
        'heroicon-o-clipboard'             => 'Clipboard',
        'heroicon-o-clipboard-document'    => 'Clipboard doc',
        'heroicon-o-clipboard-document-list' => 'Clipboard list',
        'heroicon-o-bookmark'              => 'Bookmark',
        'heroicon-o-book-open'             => 'Book',
        'heroicon-o-newspaper'             => 'Newspaper',

        // ── Media
        'heroicon-o-photo'                 => 'Photo',
        'heroicon-o-film'                  => 'Film',
        'heroicon-o-video-camera'          => 'Video',
        'heroicon-o-musical-note'          => 'Music',
        'heroicon-o-microphone'            => 'Microphone',
        'heroicon-o-speaker-wave'          => 'Speaker',
        'heroicon-o-paint-brush'           => 'Paint brush',
        'heroicon-o-swatch'                => 'Swatch',

        // ── Communication
        'heroicon-o-envelope'              => 'Envelope',
        'heroicon-o-envelope-open'         => 'Envelope open',
        'heroicon-o-chat-bubble-left-right' => 'Chat',
        'heroicon-o-chat-bubble-oval-left-ellipsis' => 'Chat dots',
        'heroicon-o-megaphone'             => 'Megaphone',
        'heroicon-o-bell'                  => 'Bell',
        'heroicon-o-phone'                 => 'Phone (call)',

        // ── People / business
        'heroicon-o-user'                  => 'User',
        'heroicon-o-user-circle'           => 'User circle',
        'heroicon-o-user-group'            => 'People',
        'heroicon-o-users'                 => 'Users',
        'heroicon-o-identification'        => 'ID card',
        'heroicon-o-building-office'       => 'Office',
        'heroicon-o-building-office-2'     => 'Office 2',
        'heroicon-o-building-storefront'   => 'Storefront',
        'heroicon-o-briefcase'             => 'Briefcase',
        'heroicon-o-home'                  => 'Home',
        'heroicon-o-home-modern'           => 'Home modern',

        // ── Security
        'heroicon-o-shield-check'          => 'Shield',
        'heroicon-o-lock-closed'           => 'Lock',
        'heroicon-o-lock-open'             => 'Lock open',
        'heroicon-o-key'                   => 'Key',
        'heroicon-o-finger-print'          => 'Fingerprint',

        // ── Time / dates
        'heroicon-o-clock'                 => 'Clock',
        'heroicon-o-calendar'              => 'Calendar',
        'heroicon-o-calendar-days'         => 'Calendar days',

        // ── Location / world
        'heroicon-o-map'                   => 'Map',
        'heroicon-o-map-pin'               => 'Map pin',
        'heroicon-o-globe-alt'             => 'Globe',
        'heroicon-o-globe-europe-africa'   => 'Globe EU',

        // ── Data / analytics
        'heroicon-o-chart-bar'             => 'Chart bar',
        'heroicon-o-chart-pie'             => 'Chart pie',
        'heroicon-o-presentation-chart-line' => 'Chart line',
        'heroicon-o-table-cells'           => 'Table',
        'heroicon-o-circle-stack'          => 'Database',

        // ── Misc
        'heroicon-o-cloud'                 => 'Cloud',
        'heroicon-o-cloud-arrow-up'        => 'Cloud upload',
        'heroicon-o-cloud-arrow-down'      => 'Cloud download',
        'heroicon-o-sun'                   => 'Sun',
        'heroicon-o-moon'                  => 'Moon',
        'heroicon-o-cake'                  => 'Cake',
        'heroicon-o-scale'                 => 'Scale',
        'heroicon-o-link'                  => 'Link',
        'heroicon-o-bookmark-square'       => 'Bookmark sq.',
        'heroicon-o-eye'                   => 'Eye',
        'heroicon-o-magnifying-glass'      => 'Search',
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
