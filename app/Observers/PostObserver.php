<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\SitemapService;

class PostObserver
{
    public function saved(Post $post): void
    {
        // Regenerate whenever a post is created or updated
        $this->regenerate($post);
    }

    public function deleted(Post $post): void
    {
        $this->regenerate($post);
    }

    public function forceDeleted(Post $post): void
    {
        $this->regenerate($post);
    }

    private function regenerate(Post $post): void
    {
        try {
            SitemapService::generate();
        } catch (\Throwable) {
            // Never break the request if sitemap generation fails
        }
    }
}
