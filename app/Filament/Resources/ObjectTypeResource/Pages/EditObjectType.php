<?php

namespace App\Filament\Resources\ObjectTypeResource\Pages;

use App\Filament\Resources\ObjectTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditObjectType extends EditRecord
{
    protected static string $resource = ObjectTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    public function getTitle(): string
    {
        return 'Edit ' . $this->getRecord()->name;
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    public function getBreadcrumbs(): array
    {
        return [
            static::$resource::getUrl('index') => 'Object Engine',
            $this->getRecord()->name,
        ];
    }
}
