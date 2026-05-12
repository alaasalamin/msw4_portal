<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerTagResource\Pages;
use App\Models\CustomerTag;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerTagResource extends Resource
{
    protected static ?string $model = CustomerTag::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-hashtag'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'CRM'; }
    public static function getNavigationSort(): ?int                    { return 3; }
    public static function getNavigationLabel(): string                 { return 'Customer Tags'; }
    public static function getModelLabel(): string                      { return 'Customer Tag'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Free-form — e.g. VIP, Wholesale, Wholesale-EU, AI2AI.'),
            ColorPicker::make('color'),
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
            'index'  => Pages\ListCustomerTags::route('/'),
            'create' => Pages\CreateCustomerTag::route('/create'),
            'edit'   => Pages\EditCustomerTag::route('/{record}/edit'),
        ];
    }
}
