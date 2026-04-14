<?php

namespace App\Http\Middleware;

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
            ],
            'nav_pages' => SitePage::where('status', 'published')
                ->orderBy('created_at')
                ->get(['title', 'slug'])
                ->map(fn ($p) => ['label' => $p->title, 'href' => '/' . $p->slug])
                ->all(),
        ];
    }
}
