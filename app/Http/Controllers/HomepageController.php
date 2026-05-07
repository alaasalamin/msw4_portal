<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
use App\Models\Post;
use App\Models\Setting;

class HomepageController extends Controller
{
    /**
     * Returns the homepage payload consumed by Welcome.tsx, blog pages,
     * and Site Pages. Everything is driven by the Theme Builder — there
     * are no longer any standalone "homepage" content fields.
     */
    public static function content(): array
    {
        $rawSections = json_decode(Setting::get('home_sections') ?? '', true) ?: [];

        // Hydrate form sections with the actual form + fields so the React
        // component can render the inputs without an extra fetch.
        $sections = array_map(function ($s) {
            if (($s['type'] ?? null) === 'form') {
                $formId = $s['settings']['form_id'] ?? null;
                if ($formId) {
                    $form = CustomForm::with('fields')->find($formId);
                    if ($form) {
                        $s['settings']['form'] = $form->toArray();
                    }
                }
            }
            return $s;
        }, $rawSections);

        return [
            'sections' => $sections,

            // Pool of recent published posts the blog_posts section can pick from.
            'posts' => Post::with('category')
                ->published()
                ->orderBy('published_at', 'desc')
                ->limit(30)
                ->get()
                ->map(fn (Post $p) => [
                    'title'         => $p->title,
                    'excerpt'       => $p->excerpt,
                    'href'          => '/blog/' . $p->fullSlug(),
                    'image'         => $p->featured_image ? asset('storage/' . $p->featured_image) : null,
                    'publishedAt'   => $p->published_at?->toIso8601String(),
                    'category'      => $p->category ? [
                        'name' => $p->category->name,
                        'slug' => $p->category->slug,
                        'href' => '/blog/category/' . $p->category->slug,
                    ] : null,
                ])
                ->all(),
        ];
    }
}
