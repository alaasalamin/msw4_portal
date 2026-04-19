<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Models\Shipment;
use App\Models\User;
use App\Services\DhlService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-truck'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Operations'; }
    public static function getNavigationSort(): ?int                    { return 1; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Shipment Details')->schema([
                Select::make('user_id')
                    ->label('Customer')
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('tracking_number')->disabled(),
                Select::make('status')
                    ->options([
                        'label_created' => 'Label Created',
                        'pending'       => 'Pending',
                        'processing'    => 'Processing',
                        'shipped'       => 'Shipped',
                        'delivered'     => 'Delivered',
                        'cancelled'     => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
                Select::make('type')
                    ->options([
                        'domestic'      => 'Domestic (Germany)',
                        'international' => 'International',
                    ])
                    ->required()
                    ->default('domestic'),
                TextInput::make('weight_kg')->numeric()->required()->suffix('kg'),
                TextInput::make('reference'),
            ])->columns(2),

            Section::make('Sender')->schema([
                TextInput::make('sender_name')->required(),
                TextInput::make('sender_company'),
                TextInput::make('sender_email')->email(),
                TextInput::make('sender_phone'),
                TextInput::make('sender_street')->required(),
                TextInput::make('sender_house_number')->required(),
                TextInput::make('sender_postal_code')->required(),
                TextInput::make('sender_city')->required(),
                TextInput::make('sender_country')->required()->default('DEU')->maxLength(3),
            ])->columns(2)->collapsed(fn ($record) => $record !== null),

            Section::make('Recipient')->schema([
                TextInput::make('recipient_name')->required(),
                TextInput::make('recipient_company'),
                TextInput::make('recipient_email')->email(),
                TextInput::make('recipient_phone'),
                TextInput::make('recipient_street')->required(),
                TextInput::make('recipient_house_number')->required(),
                TextInput::make('recipient_postal_code')->required(),
                TextInput::make('recipient_city')->required(),
                TextInput::make('recipient_country')->required()->default('DEU')->maxLength(3),
            ])->columns(2)->collapsed(fn ($record) => $record !== null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_number')->searchable()->sortable()->copyable(),
                TextColumn::make('user.name')->label('Customer')->searchable()->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'label_created' => 'primary',
                        'pending'       => 'gray',
                        'processing'    => 'warning',
                        'shipped'       => 'info',
                        'delivered'     => 'success',
                        'cancelled'     => 'danger',
                        default         => 'gray',
                    }),
                TextColumn::make('sender_city')->label('From'),
                TextColumn::make('recipient_city')->label('To'),
                TextColumn::make('weight_kg')->label('Weight (kg)'),
                IconColumn::make('label_url')
                    ->label('Label')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-arrow-down')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'label_created' => 'Label Created',
                        'pending'       => 'Pending',
                        'processing'    => 'Processing',
                        'shipped'       => 'Shipped',
                        'delivered'     => 'Delivered',
                        'cancelled'     => 'Cancelled',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'domestic'      => 'Domestic',
                        'international' => 'International',
                    ]),
            ])
            ->actions([
                Action::make('download_label')
                    ->label('Label')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn (Shipment $record): string => $record->label_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Shipment $record): bool => filled($record->label_url)),

                Action::make('create_label')
                    ->label('Create DHL Label')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn (Shipment $record): bool => blank($record->tracking_number))
                    ->action(function (Shipment $record) {
                        $fakeTracking = '00340434442135100134';
                        $record->update([
                            'tracking_number' => $fakeTracking,
                            'label_url'       => 'https://dhl.com',
                            'status'          => 'label_created',
                        ]);
                        Notification::make()->title('DHL label created (test)')->success()->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit'   => Pages\EditShipment::route('/{record}/edit'),
            'view'   => Pages\ViewShipment::route('/{record}'),
        ];
    }
}
