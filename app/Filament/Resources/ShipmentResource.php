<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShipmentResource\Pages;
use App\Models\Shipment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                TextInput::make('tracking_number')->disabled(),
                Select::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ])
                    ->required(),
                Select::make('type')
                    ->options([
                        'standard' => 'Standard',
                        'express'  => 'Express',
                    ]),
                TextInput::make('weight_kg')->numeric(),
                TextInput::make('reference'),
            ])->columns(2),

            Section::make('Sender')->schema([
                TextInput::make('sender_name'),
                TextInput::make('sender_company'),
                TextInput::make('sender_email')->email(),
                TextInput::make('sender_phone'),
                TextInput::make('sender_street'),
                TextInput::make('sender_house_number'),
                TextInput::make('sender_postal_code'),
                TextInput::make('sender_city'),
                TextInput::make('sender_country'),
            ])->columns(2)->collapsed(),

            Section::make('Recipient')->schema([
                TextInput::make('recipient_name'),
                TextInput::make('recipient_company'),
                TextInput::make('recipient_email')->email(),
                TextInput::make('recipient_phone'),
                TextInput::make('recipient_street'),
                TextInput::make('recipient_house_number'),
                TextInput::make('recipient_postal_code'),
                TextInput::make('recipient_city'),
                TextInput::make('recipient_country'),
            ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_number')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Customer')->searchable()->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'gray',
                        'processing' => 'warning',
                        'shipped'    => 'info',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),
                TextColumn::make('sender_city')->label('From'),
                TextColumn::make('recipient_city')->label('To'),
                TextColumn::make('weight_kg')->label('Weight (kg)'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'standard' => 'Standard',
                        'express'  => 'Express',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'edit'  => Pages\EditShipment::route('/{record}/edit'),
            'view'  => Pages\ViewShipment::route('/{record}'),
        ];
    }
}
