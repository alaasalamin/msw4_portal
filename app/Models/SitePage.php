<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SitePage extends Model
{
    protected $table = 'site_pages';

    protected $fillable = ['title', 'slug', 'meta_title', 'meta_description', 'status', 'sections'];

    protected $casts = [
        'sections' => 'array',
    ];

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }
}
