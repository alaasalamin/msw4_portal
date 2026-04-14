<?php

namespace App\Observers;

use App\Models\SitePage;
use App\Services\SitemapService;

class SitePageObserver
{
    public function saved(SitePage $page): void
    {
        $this->regenerate();
    }

    public function deleted(SitePage $page): void
    {
        $this->regenerate();
    }

    private function regenerate(): void
    {
        try {
            SitemapService::generate();
        } catch (\Throwable) {
            //
        }
    }
}
