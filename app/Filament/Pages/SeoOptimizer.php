<?php

namespace App\Filament\Pages;

use App\Models\Post;
use App\Models\Setting;
use App\Models\SitePage;
use App\Services\SitemapService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

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

    /**
     * Initial state for the live snippet preview block in the blade.
     * Alpine reads these and lets the user edit them in place.
     */
    public function getSnippetState(): array
    {
        $companyName = Setting::get('company_name') ?: Setting::get('site_name', config('app.name', 'MSW4'));
        return [
            'title'       => Setting::get('seo_title') ?: ($companyName . ' — Home'),
            'description' => Setting::get('seo_description', Setting::get('site_description', '')),
            'url'         => rtrim(config('app.url') ?: url('/'), '/'),
            'siteName'    => $companyName,
        ];
    }

    /**
     * Persist the snippet edits. Called from the blade via $wire.call.
     * Updates the same settings keys app.blade.php already emits.
     */
    public function saveSnippet(string $title, string $description): void
    {
        $title = trim($title);
        $description = trim($description);

        Setting::set('seo_title',       $title);
        Setting::set('seo_description', $description);
        // Keep the OG variants in sync when the user hasn't customized them separately.
        if (! Setting::get('og_title'))       Setting::set('og_title', $title);
        if (! Setting::get('og_description')) Setting::set('og_description', $description);

        Notification::make()
            ->title('Snippet saved')
            ->body('Google will pick this up next time it crawls.')
            ->success()
            ->send();
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

    /**
     * Builds the Google-style search-result snippet HTML used inside the
     * editMeta modal. Mirrors the live Alpine preview on the page itself,
     * but rendered server-side (because Filament Placeholders re-render via
     * Livewire — the live(debounce:200) on the inputs drives the refresh).
     */
    protected function renderSnippetHtml(string $title, string $description, string $url, string $siteName): string
    {
        $title       = trim($title);
        $description = trim($description);
        $siteName    = trim($siteName) ?: 'Site';

        // Pretty breadcrumb: host + path (no scheme, no trailing slash).
        $prettyUrl = $url;
        $host = $url;
        $parsed = @parse_url($url);
        if (is_array($parsed) && ! empty($parsed['host'])) {
            $host = $parsed['host'];
            $path = isset($parsed['path']) && $parsed['path'] !== '/' ? $parsed['path'] : '';
            $prettyUrl = $host . $path;
        }

        $initial = strtoupper(mb_substr($siteName, 0, 1, 'UTF-8'));
        $t = e($title);
        $d = e($description);
        $sn = e($siteName);
        $pu = e($prettyUrl);
        $hh = e($host);
        $i = e($initial);

        return <<<HTML
<div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:14px;
            padding:16px 18px; display:flex; flex-direction:column; gap:6px;
            box-shadow:0 1px 2px rgba(15,23,42,.04);
            font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;">
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
        <div style="width:18px; height:18px; border-radius:9999px; background:#e8eaed;
                    display:flex; align-items:center; justify-content:center;
                    font-size:9px; font-weight:700; color:#5f6368; text-transform:uppercase;">
            {$i}
        </div>
        <div style="display:flex; flex-direction:column; line-height:1.2;">
            <span style="font-size:12px; color:#202124; font-weight:500;">{$sn}</span>
            <span style="font-size:11px; color:#5f6368;">{$pu}</span>
        </div>
    </div>
    <a href="#" onclick="return false;" style="
        font-size:18px; line-height:1.3; color:#1a0dab; font-weight:400;
        text-decoration:none; cursor:pointer;">{$t}</a>
    <p style="margin:0; font-size:13.5px; line-height:1.55; color:#4d5156; word-wrap:break-word;">
        {$d}
    </p>
    <div style="margin-top:4px; font-size:11px; color:#5f6368;">
        <span style="opacity:.7;">⓵</span> A live preview — actual rendering varies by query, device and Google.
    </div>
</div>
HTML;
    }

    /**
     * Resolves an audit row's record (Page or Post) by its passed type+id.
     * Used by the per-row edit action.
     */
    protected function resolveRecord(?string $type, $id): ?Model
    {
        if (! $type || ! $id) return null;
        return match ($type) {
            'page' => SitePage::find($id),
            'post' => Post::find($id),
            default => null,
        };
    }

    public function editMetaAction(): Action
    {
        return Action::make('editMeta')
            ->label('Edit meta')
            ->icon('heroicon-m-pencil-square')
            ->modalWidth('2xl')
            ->modalHeading(function (array $arguments): string {
                $rec = $this->resolveRecord($arguments['type'] ?? null, $arguments['id'] ?? null);
                return $rec ? "Edit meta — {$rec->title}" : 'Edit meta';
            })
            ->modalDescription('These fields go straight to the page\'s <title> and <meta name="description">. Aim for 30–60 chars and 70–160 chars respectively.')
            ->modalSubmitActionLabel('Save')
            ->fillForm(function (array $arguments) {
                $rec = $this->resolveRecord($arguments['type'] ?? null, $arguments['id'] ?? null);
                return $rec ? [
                    'meta_title'       => $rec->meta_title,
                    'meta_description' => $rec->meta_description,
                ] : [];
            })
            ->schema(function (array $arguments) {
                $rec = $this->resolveRecord($arguments['type'] ?? null, $arguments['id'] ?? null);

                // Resolve the canonical URL for this record so the preview's
                // breadcrumb is the page Google would actually show.
                $url = url('/');
                if ($rec instanceof SitePage) {
                    $url = url('/' . $rec->slug);
                } elseif ($rec instanceof Post) {
                    $url = url('/blog/' . $rec->fullSlug());
                }
                $siteName = Setting::get('company_name')
                    ?: Setting::get('site_name', config('app.name', 'MSW4'));
                $fallbackTitle = $rec?->title ?? '';
                $fallbackDesc  = '';

                return [
                    TextInput::make('meta_title')
                        ->label('Meta title')
                        ->maxLength(120)
                        ->placeholder('Your headline as it appears in Google')
                        ->helperText('Recommended: 30–60 characters.')
                        ->live(debounce: 200),
                    Textarea::make('meta_description')
                        ->label('Meta description')
                        ->rows(4)
                        ->maxLength(280)
                        ->placeholder('A 70–160 character summary that invites the click')
                        ->helperText('Recommended: 70–160 characters.')
                        ->live(debounce: 200),
                    Placeholder::make('preview')
                        ->label('Live Google preview')
                        ->content(fn (Get $get) => new HtmlString($this->renderSnippetHtml(
                            (string) ($get('meta_title') ?: $fallbackTitle ?: 'Untitled — your meta title goes here'),
                            (string) ($get('meta_description') ?: $fallbackDesc ?: 'Your meta description shows here. Aim for 70–160 characters that summarize the page and invite the click.'),
                            $url,
                            $siteName,
                        ))),
                ];
            })
            ->action(function (array $arguments, array $data) {
                $rec = $this->resolveRecord($arguments['type'] ?? null, $arguments['id'] ?? null);
                if (! $rec) {
                    Notification::make()->title('Could not load record')->danger()->send();
                    return;
                }
                $rec->meta_title       = trim($data['meta_title']       ?? '') ?: null;
                $rec->meta_description = trim($data['meta_description'] ?? '') ?: null;
                $rec->save();

                Notification::make()
                    ->title('Meta saved')
                    ->body($rec->title)
                    ->success()
                    ->send();
            });
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
                kind: 'page',
                id: $p->id,
                type: 'Page',
                title: $p->title,
                url: '/' . $p->slug,
                metaTitle: $p->meta_title,
                metaDescription: $p->meta_description,
            );
        }

        foreach (Post::published()->orderBy('published_at', 'desc')->get() as $p) {
            $items[] = $this->auditItem(
                kind: 'post',
                id: $p->id,
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

    protected function auditItem(string $kind, int $id, string $type, string $title, string $url, ?string $metaTitle, ?string $metaDescription): array
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
            'kind'            => $kind,
            'id'              => $id,
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
