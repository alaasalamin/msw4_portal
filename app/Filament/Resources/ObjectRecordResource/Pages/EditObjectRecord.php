<?php

namespace App\Filament\Resources\ObjectRecordResource\Pages;

use App\Filament\Resources\ObjectRecordResource;
use App\Services\EngineContractRenderer;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        return [
            Action::make('downloadContract')
                ->label('Contract')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->visible(fn (): bool => filled($this->getRecord()->type?->contract_template))
                ->action(function (): StreamedResponse {
                    $record = $this->getRecord();
                    $pdf    = app(EngineContractRenderer::class)->pdf($record);

                    $name = Str::slug(($record->type?->name ?? 'contract') . '-' . $record->id) . '.pdf';

                    return response()->streamDownload(
                        fn () => print($pdf),
                        $name,
                        ['Content-Type' => 'application/pdf'],
                    );
                }),
            DeleteAction::make(),
        ];
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
