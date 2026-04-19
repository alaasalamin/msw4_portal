<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Contact;
use App\Models\Device;
use App\Models\Setting;
use App\Models\WorkflowPhase;
use App\Services\DhlService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\GlobalSearch\GlobalSearchResult;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-device-phone-mobile'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Operations'; }
    public static function getNavigationSort(): ?int                    { return 2; }

    // ── Global search ─────────────────────────────────────────────────────────

    public static function getGloballySearchableAttributes(): array
    {
        return ['ticket_number', 'storage_box', 'brand', 'model', 'contact.name', 'contact.email', 'contact.phone'];
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->ticket_number . ' — ' . $record->brand . ' ' . $record->model;
    }

    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        $details = ['Kunde' => $record->customer_name ?: '—'];

        if ($record->storage_box) {
            $details['Box'] = $record->storage_box;
        }

        if ($record->workflowStep) {
            $details['Schritt'] = $record->workflowStep->label;
        }

        return $details;
    }

    public static function getGlobalSearchResultUrl(\Illuminate\Database\Eloquent\Model $record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['workflowStep', 'contact']);
    }

    // ─────────────────────────────────────────────────────────────────────────

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Ticket Info')->schema([
                TextInput::make('ticket_number')->disabled(),
                Select::make('workflow_step_id')
                    ->label('Aktueller Schritt')
                    ->options(function () {
                        return WorkflowPhase::with(['steps' => fn ($q) => $q->orderBy('sort_order')])
                            ->orderBy('sort_order')
                            ->get()
                            ->mapWithKeys(fn ($phase) => [
                                $phase->label => $phase->steps->pluck('label', 'id'),
                            ]);
                    })
                    ->searchable()
                    ->nullable(),
                Select::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'normal' => 'Normal',
                        'high'   => 'High',
                        'urgent' => 'Urgent',
                    ]),
                DateTimePicker::make('received_at'),
                DateTimePicker::make('estimated_completion'),
                DateTimePicker::make('completed_at'),
            ])->columns(2),

            Section::make('Customer')->schema([
                Select::make('contact_id')
                    ->label('Customer')
                    ->options(Contact::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        TextInput::make('email')->email(),
                        TextInput::make('phone'),
                        TextInput::make('street'),
                        TextInput::make('house_number')->label('House No.'),
                        TextInput::make('postal_code'),
                        TextInput::make('city'),
                    ])
                    ->createOptionUsing(fn (array $data) => Contact::create($data)->id),
            ])->columns(1),

            Section::make('Device')->schema([
                TextInput::make('brand'),
                TextInput::make('model'),
                TextInput::make('serial_number'),
                TextInput::make('color'),
                TextInput::make('storage_box')
                    ->label('Box / Lagerplatz')
                    ->placeholder('z.B. Box 123, Regal A2')
                    ->maxLength(60)
                    ->prefix('📦'),
            ])->columns(2),

            Section::make('Repair')->schema([
                Textarea::make('issue_description')->rows(3),
                Textarea::make('internal_notes')->rows(3),
                TextInput::make('estimated_cost')->numeric()->prefix('€'),
                TextInput::make('final_cost')->numeric()->prefix('€'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')->searchable()->sortable(),
                TextColumn::make('contact.name')->label('Customer')->searchable(),
                TextColumn::make('brand'),
                TextColumn::make('model'),
                TextColumn::make('storage_box')
                    ->label('Box')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-m-archive-box')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('workflowStep.label')
                    ->label('Schritt')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'gray',
                        'normal' => 'info',
                        'high'   => 'warning',
                        'urgent' => 'danger',
                        default  => 'gray',
                    }),
                TextColumn::make('final_cost')->money('EUR'),
                TextColumn::make('received_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('workflow_step_id')
                    ->label('Schritt')
                    ->relationship('workflowStep', 'label'),
                SelectFilter::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'normal' => 'Normal',
                        'high'   => 'High',
                        'urgent' => 'Urgent',
                    ]),
            ])
            ->actions([
                Action::make('download_dhl_label')
                    ->label('DHL Label')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Device $record): string => $record->dhl_label_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Device $record): bool => filled($record->dhl_label_url)),

                Action::make('generate_dhl_label')
                    ->label('Generate DHL Label')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Generate DHL Label')
                    ->modalDescription('Creates a 0.2 kg DHL shipment from company address to the customer address.')
                    ->visible(fn (Device $record): bool => blank($record->dhl_label_url))
                    ->action(function (Device $record) {
                        $contact = $record->contact;

                        if (! $contact) {
                            Notification::make()->title('No customer linked to this device')->danger()->send();
                            return;
                        }

                        if (blank($contact->street) || blank($contact->postal_code) || blank($contact->city)) {
                            Notification::make()->title('Customer address is incomplete')->body('Please add street, postal code and city to the customer profile.')->danger()->send();
                            return;
                        }

                        try {
                            $result = app(DhlService::class)->createShipment([
                                'type'                   => 'domestic',
                                'sender_name'            => Setting::get('company_owner_name'),
                                'sender_company'         => Setting::get('company_name'),
                                'sender_email'           => Setting::get('company_email'),
                                'sender_phone'           => Setting::get('company_phone'),
                                'sender_street'          => Setting::get('company_street'),
                                'sender_house_number'    => Setting::get('company_house_number'),
                                'sender_postal_code'     => Setting::get('company_postal_code'),
                                'sender_city'            => Setting::get('company_city'),
                                'sender_country'         => 'DEU',
                                'recipient_name'         => $contact->name,
                                'recipient_street'       => $contact->street,
                                'recipient_house_number' => $contact->house_number,
                                'recipient_postal_code'  => $contact->postal_code,
                                'recipient_city'         => $contact->city,
                                'recipient_country'      => 'DEU',
                                'recipient_email'        => $contact->email,
                                'recipient_phone'        => $contact->phone,
                                'weight_kg'              => 0.2,
                                'reference'              => $record->ticket_number,
                            ]);

                            $record->update([
                                'dhl_tracking_number' => $result['tracking_number'],
                                'dhl_label_url'       => $result['label_url'],
                            ]);

                            Notification::make()->title('DHL label generated')->success()->send();
                        } catch (\RuntimeException $e) {
                            Notification::make()->title('DHL Error')->body($e->getMessage())->danger()->persistent()->send();
                        }
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'edit'  => Pages\EditDevice::route('/{record}/edit'),
            'view'  => Pages\ViewDevice::route('/{record}'),
        ];
    }
}
