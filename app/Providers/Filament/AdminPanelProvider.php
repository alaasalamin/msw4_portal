<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use App\Filament\Widgets\ActivityLogWidget;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Support\Facades\Blade;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            fn () => Blade::render('<x-admin-notification-bell /><x-admin-nav-preferences />'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn () => Blade::render('<x-admin-echo-setup />'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            function (): string {
                $hidden = auth('employee')->user()?->nav_preferences['hidden_groups'] ?? [];
                if (empty($hidden)) return '';

                $css = implode('', array_map(
                    fn (string $g) => '[data-group-label="' . e($g) . '"]{display:none!important;}',
                    $hidden,
                ));

                return "<style>{$css}</style>";
            },
        );

        // Rail-style sidebar — collapsed to icons by default on desktop,
        // expands smoothly on hover. The sidebar is pulled out of the
        // document flow with position: fixed so the main content area
        // doesn't shift when the sidebar widens; .fi-main-ctn carries a
        // left margin equal to the rail width to compensate. Mobile
        // (< 1024px) keeps Filament's default overlay behaviour.
        FilamentView::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn () => <<<'BLADE'
<style>
    @media (min-width: 1024px) {
        :root { --tb-rail: 72px; --tb-rail-open: 260px; }

        /* Pull the sidebar out of the layout so width changes don't
           push the main content. Lock it to the rail width by default. */
        .fi-sidebar {
            position: fixed !important;
            top: 0; bottom: 0; left: 0;
            width: var(--tb-rail) !important;
            transform: none !important;
            z-index: 40;
            overflow: hidden;
            transition: width 280ms cubic-bezier(0.22, 0.61, 0.36, 1),
                        box-shadow 200ms ease;
        }

        /* Reserve the rail width on the main content area. */
        .fi-main-ctn { margin-left: var(--tb-rail) !important; }

        /* Fade out everything that doesn't fit in 72px while collapsed.
           Smooth transitions so re-entering the sidebar feels fluid. */
        .fi-sidebar .fi-sidebar-item-label,
        .fi-sidebar .fi-sidebar-group-label,
        .fi-sidebar .fi-sidebar-item-badge-ctn,
        .fi-sidebar .fi-sidebar-database-notifications-btn-label,
        .fi-sidebar .fi-sidebar-database-notifications-btn-badge-ctn,
        .fi-sidebar .fi-sidebar-group-collapse-btn,
        .fi-sidebar .fi-logo {
            opacity: 0;
            pointer-events: none;
            white-space: nowrap;
            transition: opacity 220ms ease;
        }

        /* Hide Filament's sidebar collapse toggle — hover replaces it. */
        .fi-sidebar-close-collapse-sidebar-btn,
        .fi-sidebar-open-collapse-sidebar-btn,
        .fi-layout-sidebar-toggle-btn-ctn {
            display: none !important;
        }

        /* Center every nav button inside the rail so icons sit in the
           middle of the 72px column rather than hugging the start edge. */
        .fi-sidebar .fi-sidebar-item-btn,
        .fi-sidebar .fi-sidebar-database-notifications-btn,
        .fi-sidebar .fi-sidebar-group-dropdown-trigger-btn,
        .fi-sidebar .fi-sidebar-group-btn {
            justify-content: center !important;
        }

        /* Hover state — expand to full width, light shadow, reveal copy. */
        .fi-sidebar:hover {
            width: var(--tb-rail-open) !important;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
        }
        html.dark .fi-sidebar:hover {
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.45);
        }
        .fi-sidebar:hover .fi-sidebar-item-btn,
        .fi-sidebar:hover .fi-sidebar-database-notifications-btn,
        .fi-sidebar:hover .fi-sidebar-group-dropdown-trigger-btn,
        .fi-sidebar:hover .fi-sidebar-group-btn {
            justify-content: flex-start !important;
        }
        .fi-sidebar:hover .fi-sidebar-item-label,
        .fi-sidebar:hover .fi-sidebar-group-label,
        .fi-sidebar:hover .fi-sidebar-item-badge-ctn,
        .fi-sidebar:hover .fi-sidebar-database-notifications-btn-label,
        .fi-sidebar:hover .fi-sidebar-database-notifications-btn-badge-ctn,
        .fi-sidebar:hover .fi-sidebar-group-collapse-btn,
        .fi-sidebar:hover .fi-logo {
            opacity: 1;
            pointer-events: auto;
            transition-delay: 80ms;
        }
    }
</style>
BLADE,
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('employee')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->brandName('MSW 4')
            ->navigationGroups([
                NavigationGroup::make('Operations'),
                NavigationGroup::make('Workflow'),
                NavigationGroup::make('Content'),
                NavigationGroup::make('Blog'),
                NavigationGroup::make('User Management'),
                NavigationGroup::make('Configuration'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                ActivityLogWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
