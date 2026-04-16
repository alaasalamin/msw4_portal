<?php

namespace App\Filament\Resources\CustomFormResource\Pages;

use App\Filament\Resources\CustomFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomForms extends ListRecords
{
    protected static string $resource = CustomFormResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
