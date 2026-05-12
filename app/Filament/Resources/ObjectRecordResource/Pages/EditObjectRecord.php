<?php

namespace App\Filament\Resources\ObjectRecordResource\Pages;

use App\Filament\Resources\ObjectRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\Url;

class EditObjectRecord extends EditRecord
{
    protected static string $resource = ObjectRecordResource::class;

    #[Url(keep: true)]
    public ?string $type = null;

    public function mount(int | string $record): void
    {
        ObjectRecordResource::$currentTypeSlug = $this->type ?: (string) request()->query('type');
        parent::mount($record);
    }

    public function boot(): void
    {
        ObjectRecordResource::$currentTypeSlug = $this->type ?: ObjectRecordResource::$currentTypeSlug;
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    public function getTitle(): string
    {
        $type = ObjectRecordResource::activeType();
        $name = $type?->name ?? 'Object Record';
        return '#' . $this->getRecord()->id . ' ' . $name;
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    public function getBreadcrumbs(): array
    {
        $type = ObjectRecordResource::activeType();
        if (! $type) return parent::getBreadcrumbs();

        return [
            static::$resource::getUrl('index', ['type' => $type->slug]) => $type->name,
            $this->getBreadcrumb(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $type = ObjectRecordResource::activeType();
        return $type
            ? static::$resource::getUrl('index', ['type' => $type->slug])
            : static::$resource::getUrl('index');
    }
}
