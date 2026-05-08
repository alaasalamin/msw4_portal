<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
use App\Models\SitePage;
use Inertia\Inertia;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = SitePage::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Legacy block-editor sections (form_block in particular needs the form
        // hydrated with its fields).
        $legacySections = collect($page->sections ?? [])->map(function ($section) use ($slug) {
            if (($section['type'] ?? '') === 'form_block' && ! empty($section['data']['form_id'])) {
                $form = CustomForm::with('fields')->find($section['data']['form_id']);
                if ($form) {
                    $section['data']['form']      = $form;
                    $section['data']['page_slug'] = $slug;
                }
            }
            return $section;
        })->all();

        // Theme Builder sections (the new preferred render path). Form sections
        // get the same hydration as the homepage.
        $themeSections = collect($page->theme_sections ?? [])->map(function ($section) {
            if (($section['type'] ?? null) === 'form') {
                $formId = $section['settings']['form_id'] ?? null;
                if ($formId) {
                    $form = CustomForm::with('fields')->find($formId);
                    if ($form) {
                        $section['settings']['form'] = $form->toArray();
                    }
                }
            }
            return $section;
        })->all();

        return Inertia::render('DynamicPage', [
            'page'     => array_merge($page->toArray(), [
                'sections'       => $legacySections,
                'theme_sections' => $themeSections,
            ]),
            'homepage' => HomepageController::content(),
        ]);
    }
}
