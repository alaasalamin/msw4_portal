<?php

namespace App\Filament\Resources\ObjectRecordResource\Pages;

use App\Filament\Resources\ObjectRecordResource;
use App\Models\ObjectType;
use Filament\Resources\Pages\CreateRecord;

class CreateObjectRecord extends CreateRecord
{
    protected static string $resource = ObjectRecordResource::class;

    protected function fillForm(): void
    {
        parent::fillForm();

        // When arriving from a per-type "New X" button, pre-pick the type
        // so the dynamic attribute fields render immediately.
        $typeId = (int) request()->query('type');
        if ($typeId > 0 && ObjectType::whereKey($typeId)->exists()) {
            $this->form->fill(['object_type_id' => $typeId]);
        }
    }

    public function getTitle(): string
    {
        $typeId = (int) (request()->query('type') ?? data_get($this->data ?? [], 'object_type_id'));
        $type = $typeId > 0 ? ObjectType::find($typeId) : null;
        return $type ? "New {$type->name}" : parent::getTitle();
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }
}
