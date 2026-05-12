<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ObjectEngine extends Page
{
    protected string $view = 'filament.pages.object-engine';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-cube-transparent'; }
    public static function getNavigationSort(): ?int                    { return 99; }
    public static function getNavigationLabel(): string                 { return 'Object Engine'; }
    public function getTitle(): string                                  { return 'Object Engine'; }
}
