<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerGroupResource\Pages;
use App\Models\CustomerGroup;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerGroupResource extends Resource
{
    protected static ?string $model = CustomerGroup::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-tag'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'User Management'; }
    public static function getNavigationSort(): ?int                    { return 4; }
    public static function getNavigationLabel(): string                 { return 'Customer Groups'; }
    public static function getModelLabel(): string                      { return 'Customer Group'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Anything you want — e.g. B2C, B2B, AI2AI, Wholesale.'),
            ColorPicker::make('color')
                ->helperText('Used for the group badge in the customer list.'),
            TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')
                    ->badge()
                    ->color(fn ($record) => $record->color ? null : 'gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')->toggleable(),
                TextColumn::make('sort_order')->sortable(),
                TextColumn::make('customers_count')
                    ->counts('customers')
                    ->label('Customers')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomerGroups::route('/'),
            'create' => Pages\CreateCustomerGroup::route('/create'),
            'edit'   => Pages\EditCustomerGroup::route('/{record}/edit'),
        ];
    }
}
