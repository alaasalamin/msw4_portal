<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeviceResource\Pages;
use App\Models\Device;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class DeviceResource extends Resource
{
    protected static ?string $model = Device::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-device-phone-mobile'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Operations'; }
    public static function getNavigationSort(): ?int                    { return 2; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Ticket Info')->schema([
                TextInput::make('ticket_number')->disabled(),
                Select::make('status')
                    ->options([
                        'received'   => 'Received',
                        'diagnosing' => 'Diagnosing',
                        'repairing'  => 'Repairing',
                        'waiting'    => 'Waiting for Parts',
                        'ready'      => 'Ready for Pickup',
                        'completed'  => 'Completed',
                        'cancelled'  => 'Cancelled',
                    ])
                    ->required(),
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
                TextInput::make('customer_name'),
                TextInput::make('customer_email')->email(),
                TextInput::make('customer_phone'),
            ])->columns(3),

            Section::make('Device')->schema([
                TextInput::make('brand'),
                TextInput::make('model'),
                TextInput::make('serial_number'),
                TextInput::make('color'),
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
                TextColumn::make('customer_name')->searchable(),
                TextColumn::make('brand'),
                TextColumn::make('model'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'received'   => 'gray',
                        'diagnosing' => 'info',
                        'repairing'  => 'warning',
                        'waiting'    => 'danger',
                        'ready'      => 'success',
                        'completed'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),
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
                SelectFilter::make('status')
                    ->options([
                        'received'   => 'Received',
                        'diagnosing' => 'Diagnosing',
                        'repairing'  => 'Repairing',
                        'waiting'    => 'Waiting for Parts',
                        'ready'      => 'Ready for Pickup',
                        'completed'  => 'Completed',
                        'cancelled'  => 'Cancelled',
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'normal' => 'Normal',
                        'high'   => 'High',
                        'urgent' => 'Urgent',
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
            'index' => Pages\ListDevices::route('/'),
            'edit'  => Pages\EditDevice::route('/{record}/edit'),
            'view'  => Pages\ViewDevice::route('/{record}'),
        ];
    }
}
