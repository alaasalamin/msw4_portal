<?php

namespace App\Filament\Resources\SitePageResource\Pages;

use App\Filament\Resources\SitePageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSitePage extends EditRecord
{
    protected static string $resource = SitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('preview')
                ->label('Preview Page')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => '/' . $this->getRecord()->slug)
                ->openUrlInNewTab()
                ->color('gray'),
            DeleteAction::make(),
        ];
    }
}
