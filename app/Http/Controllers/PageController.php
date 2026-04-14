<?php

namespace App\Http\Controllers;

use App\Models\SitePage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = SitePage::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return Inertia::render('DynamicPage', [
            'page'     => $page,
            'homepage' => HomepageController::content(),
        ]);
    }
}
