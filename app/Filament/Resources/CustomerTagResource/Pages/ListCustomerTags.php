<?php

namespace App\Filament\Resources\CustomerTagResource\Pages;

use App\Filament\Resources\CustomerTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomerTags extends ListRecords
{
    protected static string $resource = CustomerTagResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
