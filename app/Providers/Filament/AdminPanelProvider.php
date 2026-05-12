<?php

namespace App\Providers\Filament;

use App\Filament\Resources\ObjectRecordResource;
use App\Models\ObjectRecord;
use App\Models\ObjectType;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
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

        // Dynamic sidebar items — one per ObjectType, linking to its filtered
        // ObjectRecord list. Runs on every request after the panel is wired,
        // so newly-created object types appear without a restart/cache clear.
        Filament::serving(function () {
            try {
                $items = ObjectType::orderBy('sort_order')->orderBy('name')
                    ->get()
                    ->map(fn (ObjectType $type) => NavigationItem::make($type->name)
                        ->icon($type->icon ?: 'heroicon-o-cube')
                        ->group('Object Engine')
                        ->sort(10 + $type->id)
                        ->url(ObjectRecordResource::getUrl('index', [
                            'filters' => ['object_type_id' => ['value' => $type->id]],
                        ]))
                        ->isActiveWhen(function () use ($type): bool {
                            if (! request()->routeIs('filament.admin.resources.object-records.*')) {
                                return false;
                            }

                            // Index page filtered by this type
                            $filterId = (int) data_get(request()->query(), 'filters.object_type_id.value');
                            if ($filterId === (int) $type->id) return true;

                            // Create page launched from this type (?type=<id>)
                            $createTypeId = (int) request()->query('type');
                            if (request()->routeIs('filament.admin.resources.object-records.create')
                                && $createTypeId === (int) $type->id) {
                                return true;
                            }

                            // Edit page for a record of this type
                            $recordId = request()->route('record');
                            if ($recordId && request()->routeIs('filament.admin.resources.object-records.edit')) {
                                $ownerTypeId = (int) ObjectRecord::whereKey($recordId)->value('object_type_id');
                                if ($ownerTypeId === (int) $type->id) return true;
                            }

                            return false;
                        }))
                    ->all();

                Filament::registerNavigationItems($items);
            } catch (\Throwable) {
                // Migrations may not have run yet (deploy timing) — skip silently.
            }
        });

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

        // Sidebar hover-to-expand: when collapsed (no .fi-sidebar-open), take it
        // out of flow (position: fixed) and reserve its width on .fi-main-ctn so
        // hover-expand overlays the main content without shifting it.
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => <<<'HTML'
                <style>
                    @media (min-width: 1024px) {
                        .fi-main-sidebar:not(.fi-sidebar-open) {
                            position: fixed;
                            inset-block-start: 4rem; /* sit below the topbar */
                            inset-block-end: 0;
                            inset-inline-start: 0;
                            height: calc(100vh - 4rem);
                            z-index: 40;
                            /* Solid theme background so hover-expand doesn't bleed through main content. */
                            background-color: var(--color-white, #ffffff) !important;
                            transition: width .45s cubic-bezier(0.32, 0.72, 0, 1),
                                        box-shadow .45s cubic-bezier(0.32, 0.72, 0, 1);
                        }
                        .dark .fi-main-sidebar:not(.fi-sidebar-open),
                        html.dark .fi-main-sidebar:not(.fi-sidebar-open) {
                            background-color: var(--gray-900, #18181b) !important;
                        }
                        /* Reserve space for the now-absolutely-positioned collapsed sidebar.
                           5rem matches Filament's collapsed width. */
                        .fi-main-sidebar:not(.fi-sidebar-open) ~ .fi-main-ctn {
                            margin-inline-start: 5rem;
                        }
                        .fi-main-sidebar:not(.fi-sidebar-open):hover {
                            width: 16rem !important;
                            box-shadow: 0 12px 28px rgba(0, 0, 0, .12);
                        }
                        .dark .fi-main-sidebar:not(.fi-sidebar-open):hover,
                        html.dark .fi-main-sidebar:not(.fi-sidebar-open):hover {
                            box-shadow: 0 12px 28px rgba(0, 0, 0, .45);
                        }
                        .fi-main-sidebar:not(.fi-sidebar-open):hover .fi-sidebar-item-label,
                        .fi-main-sidebar:not(.fi-sidebar-open):hover .fi-sidebar-group-label,
                        .fi-main-sidebar:not(.fi-sidebar-open):hover .fi-sidebar-header-logo-ctn {
                            opacity: 1 !important;
                            visibility: visible !important;
                            display: inline-flex !important;
                        }
                    }
                </style>
            HTML,
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
                'purple'  => Color::Purple,
            ])
            ->brandName('Bizo')
            ->navigationGroups([
                NavigationGroup::make('Operations'),
                NavigationGroup::make('Workflow'),
                NavigationGroup::make('Content'),
                NavigationGroup::make('Blog'),
                NavigationGroup::make('CRM'),
                NavigationGroup::make('Object Engine'),
                NavigationGroup::make('User Management'),
                NavigationGroup::make('Configuration'),
            ])
            ->sidebarCollapsibleOnDesktop()
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
