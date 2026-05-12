<?php

namespace App\Filament\Resources\ObjectRecordResource\Pages;

use App\Filament\Resources\ObjectRecordResource;
use App\Models\EngineRecord;
use App\Models\ObjectType;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

class CreateObjectRecord extends CreateRecord
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

    /**
     * Insert into the active type's table.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $type = ObjectRecordResource::activeType();
        abort_unless($type, 404);

        $record = EngineRecord::forType($type);
        $record->fill($data)->save();
        return $record;
    }

    public function getTitle(): string
    {
        return ($t = ObjectRecordResource::activeType()) ? "New {$t->name}" : parent::getTitle();
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
