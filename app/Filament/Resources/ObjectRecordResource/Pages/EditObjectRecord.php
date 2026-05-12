<?php

namespace App\Filament\Resources\ObjectRecordResource\Pages;

use App\Filament\Resources\ObjectRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditObjectRecord extends EditRecord
{
    protected static string $resource = ObjectRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
