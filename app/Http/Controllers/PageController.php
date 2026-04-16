<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
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

        // Hydrate form_block sections with actual form + fields data
        $sections = collect($page->sections ?? [])->map(function ($section) use ($slug) {
            if (($section['type'] ?? '') === 'form_block' && ! empty($section['data']['form_id'])) {
                $form = CustomForm::with('fields')->find($section['data']['form_id']);
                if ($form) {
                    $section['data']['form']      = $form;
                    $section['data']['page_slug'] = $slug;
                }
            }
            return $section;
        })->all();

        return Inertia::render('DynamicPage', [
            'page'     => array_merge($page->toArray(), ['sections' => $sections]),
            'homepage' => HomepageController::content(),
        ]);
    }
}
