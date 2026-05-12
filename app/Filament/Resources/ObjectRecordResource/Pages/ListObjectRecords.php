<?php

namespace App\Filament\Resources\ObjectRecordResource\Pages;

use App\Filament\Resources\ObjectRecordResource;
use App\Models\ObjectType;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\Url;

class ListObjectRecords extends ListRecords
{
    protected static string $resource = ObjectRecordResource::class;

    #[Url(keep: true)]
    public ?string $type = null;

    public function mount(): void
    {
        parent::mount();
        ObjectRecordResource::$currentTypeSlug = $this->type;
    }

    public function boot(): void
    {
        ObjectRecordResource::$currentTypeSlug = $this->type;
    }

    protected function getHeaderActions(): array
    {
        $type = $this->scopedType();
        $action = CreateAction::make();

        if ($type) {
            $action
                ->label("New {$type->name}")
                ->url(static::$resource::getUrl('create', ['type' => $type->slug]));
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
            static::$resource::getUrl('index', ['type' => $type->slug]) => $type->name,
            $this->getBreadcrumb(),
        ];
    }

    protected function scopedType(): ?ObjectType
    {
        return ObjectRecordResource::activeType();
    }
}
