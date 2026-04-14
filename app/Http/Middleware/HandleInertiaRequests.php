<?php

namespace App\Http\Middleware;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Setting;
use App\Models\SitePage;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $logo = Setting::get('logo');

        return [
            ...parent::share($request),
            'auth' => [
                'user'     => $request->user(),
                'customer' => $request->user('customer'),
                'partner'  => $request->user('partner'),
                'employee' => $request->user('employee'),
            ],
            'site' => [
                'name'        => Setting::get('site_name', config('app.name')),
                'description' => Setting::get('site_description'),
                'logo'        => $logo ? asset('storage/' . $logo) : null,
                'socials'     => array_filter([
                    'facebook'  => Setting::get('social_facebook'),
                    'instagram' => Setting::get('social_instagram'),
                    'twitter'   => Setting::get('social_twitter'),
                    'linkedin'  => Setting::get('social_linkedin'),
                    'youtube'   => Setting::get('social_youtube'),
                    'tiktok'    => Setting::get('social_tiktok'),
                    'whatsapp'  => Setting::get('social_whatsapp'),
                ]),
            ],
            'nav_pages' => SitePage::where('status', 'published')
                ->orderBy('created_at')
                ->get(['title', 'slug'])
                ->map(fn ($p) => ['label' => $p->title, 'href' => '/' . $p->slug])
                ->all(),

            'footer_pages' => SitePage::where('status', 'published')
                ->orderBy('created_at')
                ->get(['title', 'slug'])
                ->map(fn ($p) => ['title' => $p->title, 'href' => '/' . $p->slug])
                ->all(),

            'footer_categories' => PostCategory::with([
                    'posts' => fn ($q) => $q->published()
                        ->orderBy('published_at', 'desc')
                        ->select(['id', 'category_id', 'title', 'slug']),
                ])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->filter(fn ($cat) => $cat->posts->isNotEmpty())
                ->map(fn ($cat) => [
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                    'posts' => $cat->posts->map(fn ($p) => [
                        'title' => $p->title,
                        'href'  => '/blog/' . $cat->slug . '/' . $p->slug,
                    ])->all(),
                ])
                ->values()
                ->all(),
        ];
    }
}
