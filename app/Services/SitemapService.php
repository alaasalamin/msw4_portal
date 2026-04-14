<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\SitePage;

class SitemapService
{
    public static function generate(): void
    {
        $base = rtrim(config('app.url'), '/');
        $now  = now()->toAtomString();

        $urls = [];

        // ── Homepage ─────────────────────────────────────────────────────────
        $urls[] = [
            'loc'        => $base . '/',
            'lastmod'    => $now,
            'changefreq' => 'weekly',
            'priority'   => '1.0',
        ];

        // ── Published CMS pages ───────────────────────────────────────────────
        foreach (SitePage::where('status', 'published')->orderBy('updated_at')->get() as $page) {
            $urls[] = [
                'loc'        => $base . '/' . $page->slug,
                'lastmod'    => $page->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority'   => '0.8',
            ];
        }

        // ── Blog category pages ───────────────────────────────────────────────
        $categories = PostCategory::whereHas('posts', fn ($q) => $q->published())
            ->orderBy('sort_order')
            ->get();

        foreach ($categories as $cat) {
            $urls[] = [
                'loc'        => $base . '/blog/category/' . $cat->slug,
                'lastmod'    => $now,
                'changefreq' => 'weekly',
                'priority'   => '0.7',
            ];
        }

        // ── Published blog posts ──────────────────────────────────────────────
        Post::with('category')
            ->published()
            ->orderBy('published_at')
            ->each(function (Post $post) use ($base, &$urls) {
                $catSlug = $post->category?->slug;
                $path    = $catSlug
                    ? "/blog/{$catSlug}/{$post->slug}"
                    : "/blog/{$post->slug}";

                $urls[] = [
                    'loc'        => $base . $path,
                    'lastmod'    => $post->updated_at->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority'   => '0.6',
                ];
            });

        // ── Build XML ─────────────────────────────────────────────────────────
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        file_put_contents(public_path('sitemap.xml'), $xml);
    }
}
