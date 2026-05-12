<?php

namespace App\Filament\Resources\ObjectRecordResource\Pages;

use App\Filament\Resources\ObjectRecordResource;
use App\Models\ObjectType;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListObjectRecords extends ListRecords
{
    protected static string $resource = ObjectRecordResource::class;

    protected function getHeaderActions(): array
    {
        $type = $this->scopedType();

        $action = CreateAction::make();

        if ($type) {
            $action
                ->label("New {$type->name}")
                ->url(static::$resource::getUrl('create', ['type' => $type->id]));
        }

        return [$action];
    }

    public function getTitle(): string
    {
        return $this->scopedType()?->name ?? parent::getTitle();
    }

    public function getHeading(): string
    {
        return $this->scopedType()?->name ?? parent::getHeading();
    }

    public function getBreadcrumbs(): array
    {
        $type = $this->scopedType();
        if (! $type) return parent::getBreadcrumbs();

        return [
            static::$resource::getUrl('index', [
                'filters' => ['object_type_id' => ['value' => $type->id]],
            ]) => $type->name,
            $this->getBreadcrumb(),
        ];
    }

    /**
     * Resolve the ObjectType from the active table filter (set by the dynamic
     * sidebar items). Cached per-request.
     */
    protected function scopedType(): ?ObjectType
    {
        static $resolved = false;
        static $type = null;
        if ($resolved) return $type;

        $resolved = true;
        $id = (int) (
            data_get(request()->query(), 'filters.object_type_id.value')
            ?? data_get($this->tableFilters ?? [], 'object_type_id.value')
        );
        if ($id <= 0) return $type = null;

        return $type = ObjectType::find($id);
    }
}
