<?php

namespace App\Filament\Resources\CustomerTagResource\Pages;

use App\Filament\Resources\CustomerTagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomerTag extends EditRecord
{
    protected static string $resource = CustomerTagResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
