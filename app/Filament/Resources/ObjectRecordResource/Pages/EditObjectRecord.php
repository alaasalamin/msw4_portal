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

    public function getTitle(): string
    {
        $record = $this->getRecord();
        $typeName = $record->type?->name ?? 'Object Record';
        return '#' . $record->id . ' ' . $typeName;
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    public function getBreadcrumbs(): array
    {
        $type = $this->getRecord()->type;
        if (! $type) return parent::getBreadcrumbs();

        return [
            static::$resource::getUrl('index', [
                'tableFilters' => ['object_type_id' => ['value' => $type->id]],
            ]) => $type->name,
            $this->getBreadcrumb(),
        ];
    }
}
