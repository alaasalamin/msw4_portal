<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('sitemap:generate')]
#[Description('Generate public/sitemap.xml from published posts and pages')]
class GenerateSitemap extends Command
{
    public function handle(): int
    {
        SitemapService::generate();
        $this->info('Sitemap generated → ' . public_path('sitemap.xml'));
        return self::SUCCESS;
    }
}
