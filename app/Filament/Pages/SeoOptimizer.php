<?php

namespace App\Filament\Pages;

use App\Models\Post;
use App\Models\Setting;
use App\Models\SitePage;
use App\Services\SitemapService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeoOptimizer extends Page
{
    protected string $view = 'filament.pages.seo-optimizer';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-magnifying-glass-circle'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationSort(): ?int                    { return 4; }
    public static function getNavigationLabel(): string                 { return 'SEO Optimizer'; }
    public function getTitle(): string                                  { return 'SEO Optimizer'; }

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'google_site_verification' => Setting::get('google_site_verification'),
            'default_robots'           => Setting::get('default_robots', 'index, follow'),
            'org_name'                 => Setting::get('seo_org_name', Setting::get('company_name')),
            'org_url'                  => Setting::get('seo_org_url'),
            'org_logo'                 => Setting::get('seo_org_logo', Setting::get('logo')),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Search engine verification')
                    ->description('One-line verification meta tags emitted in every page <head>.')
                    ->schema([
                        TextInput::make('google_site_verification')
                            ->label('Google Search Console verification code')
                            ->maxLength(120)
                            ->placeholder('e.g. abc123XYZ-fromGoogle')
                            ->helperText('The "content" value of the meta tag Search Console gives you. The meta tag is added automatically.'),
                    ])->columns(1),

                Section::make('Default robots policy')
                    ->description('Default <meta name="robots"> for pages without their own override.')
                    ->schema([
                        Select::make('default_robots')
                            ->label('Robots policy')
                            ->options([
                                'index, follow'      => 'index, follow — public site (recommended)',
                                'index, nofollow'    => 'index, nofollow',
                                'noindex, follow'    => 'noindex, follow',
                                'noindex, nofollow'  => 'noindex, nofollow — hide from search engines',
                            ])
                            ->default('index, follow'),
                    ])->columns(1),

                Section::make('Structured data — organization')
                    ->description('Powers the JSON-LD organization snippet emitted in <head>. Helps Google show your brand correctly.')
                    ->schema([
                        TextInput::make('org_name')->label('Organization name')->maxLength(120),
                        TextInput::make('org_url')->label('Organization URL')->url()->maxLength(200)->placeholder('https://example.com'),
                        Textarea::make('org_logo')->label('Logo path or URL')->rows(2)->maxLength(200)->helperText('Optional. Defaults to the site logo.'),
                    ])->columns(1),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        Setting::set('google_site_verification', $data['google_site_verification'] ?? '');
        Setting::set('default_robots',           $data['default_robots']           ?? 'index, follow');
        Setting::set('seo_org_name',             $data['org_name']                 ?? '');
        Setting::set('seo_org_url',              $data['org_url']                  ?? '');
        Setting::set('seo_org_logo',             $data['org_logo']                 ?? '');

        Notification::make()->title('SEO settings saved')->success()->send();
    }

    public function regenerateSitemap(): void
    {
        try {
            SitemapService::generate();
            Notification::make()->title('Sitemap regenerated')->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('Sitemap failed')->body($e->getMessage())->danger()->send();
        }
    }

    /**
     * Run the SEO audit and return a flat list of items, each with its metric
     * values + status flags. Consumed by the blade view.
     *
     * @return array<int, array{
     *     type: string,
     *     title: string,
     *     url: string,
     *     metaTitle: ?string,
     *     metaDescription: ?string,
     *     issues: array<int, string>,
     *     warnings: array<int, string>,
     *     score: int,
     * }>
     */
    public function getAudit(): array
    {
        $items = [];

        foreach (SitePage::where('status', 'published')->orderBy('title')->get() as $p) {
            $items[] = $this->auditItem(
                type: 'Page',
                title: $p->title,
                url: '/' . $p->slug,
                metaTitle: $p->meta_title,
                metaDescription: $p->meta_description,
            );
        }

        foreach (Post::published()->orderBy('published_at', 'desc')->get() as $p) {
            $items[] = $this->auditItem(
                type: 'Post',
                title: $p->title,
                url: '/blog/' . $p->fullSlug(),
                metaTitle: $p->meta_title,
                metaDescription: $p->meta_description,
            );
        }

        // Sort by score ascending so the worst items surface first.
        usort($items, fn ($a, $b) => $a['score'] <=> $b['score']);
        return $items;
    }

    protected function auditItem(string $type, string $title, string $url, ?string $metaTitle, ?string $metaDescription): array
    {
        $issues   = [];
        $warnings = [];

        $titleLen = $metaTitle !== null && $metaTitle !== '' ? mb_strlen($metaTitle) : 0;
        $descLen  = $metaDescription !== null && $metaDescription !== '' ? mb_strlen($metaDescription) : 0;

        if ($titleLen === 0)        $issues[]   = 'Meta title is missing.';
        elseif ($titleLen < 30)     $warnings[] = "Meta title is short ({$titleLen} chars). Aim for 30–60.";
        elseif ($titleLen > 60)     $warnings[] = "Meta title is long ({$titleLen} chars). Aim for 30–60.";

        if ($descLen === 0)         $issues[]   = 'Meta description is missing.';
        elseif ($descLen < 70)      $warnings[] = "Meta description is short ({$descLen} chars). Aim for 70–160.";
        elseif ($descLen > 160)     $warnings[] = "Meta description is long ({$descLen} chars). Aim for 70–160.";

        // Score: 100 base, -40 per issue, -10 per warning, floor at 0.
        $score = max(0, 100 - count($issues) * 40 - count($warnings) * 10);

        return [
            'type'            => $type,
            'title'           => $title,
            'url'             => $url,
            'metaTitle'       => $metaTitle,
            'metaDescription' => $metaDescription,
            'titleLen'        => $titleLen,
            'descLen'         => $descLen,
            'issues'          => $issues,
            'warnings'        => $warnings,
            'score'           => $score,
        ];
    }

    /** Aggregate counts for the summary cards at the top of the page. */
    public function getAuditSummary(array $audit): array
    {
        $total = count($audit);
        $good  = count(array_filter($audit, fn ($i) => $i['score'] === 100));
        $issues = 0; $warnings = 0;
        foreach ($audit as $i) { $issues += count($i['issues']); $warnings += count($i['warnings']); }
        $avg = $total ? (int) round(array_sum(array_column($audit, 'score')) / $total) : 100;
        return compact('total', 'good', 'issues', 'warnings', 'avg');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save settings')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
            Action::make('regenerate')
                ->label('Regenerate sitemap')
                ->action('regenerateSitemap')
                ->icon('heroicon-o-arrow-path')
                ->color('gray'),
            Action::make('viewSitemap')
                ->label('View sitemap.xml')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url('/sitemap.xml')
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
}
