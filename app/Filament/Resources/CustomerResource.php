<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CustomerTag;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-user-group'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'CRM'; }
    public static function getNavigationSort(): ?int                    { return 1; }

    public static function form(Schema $form): Schema
    {
        return $form->components(static::formComponents());
    }

    public static function formComponents(): array
    {
        return [
            Section::make('Identity')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->extraInputAttributes(['autocomplete' => 'off', 'data-1p-ignore' => 'true']),
                    TextInput::make('phone')->tel()->maxLength(50),
                    TextInput::make('company_name')
                        ->label('Company name')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Select::make('customer_group_id')
                        ->label('Group')
                        ->options(fn () => CustomerGroup::orderBy('sort_order')->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')->required()->maxLength(255),
                            ColorPicker::make('color'),
                        ])
                        ->createOptionUsing(fn (array $data) => CustomerGroup::create($data)->id)
                        ->columnSpanFull(),
                    Select::make('tags')
                        ->label('Tags')
                        ->relationship('tags', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')->required()->maxLength(255),
                            ColorPicker::make('color'),
                        ])
                        ->createOptionUsing(fn (array $data) => CustomerTag::create($data)->id)
                        ->columnSpanFull(),
                ]),

            Section::make('Address')
                ->columns(3)
                ->schema([
                    TextInput::make('address_street')->label('Street')->maxLength(255)->columnSpan(2),
                    TextInput::make('address_building_number')->label('Building number')->maxLength(50),
                    TextInput::make('address_zip_code')->label('ZIP code')->maxLength(20),
                    TextInput::make('address_city')->label('City')->maxLength(255)->columnSpan(2),
                ]),

            Section::make('Credentials')
                ->columns(1)
                ->schema([
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context) => $context === 'create')
                        ->maxLength(255),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('company_name')->label('Company')->searchable()->toggleable(),
                TextColumn::make('group.name')
                    ->label('Group')
                    ->badge()
                    ->color(fn ($state, $record) => $record?->group?->color ? null : 'gray')
                    ->formatStateUsing(fn ($state) => $state ?: '—')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->separator(',')
                    ->toggleable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('address_city')->label('City')->searchable()->toggleable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('customer_group_id')
                    ->label('Group')
                    ->options(fn () => CustomerGroup::orderBy('sort_order')->orderBy('name')->pluck('name', 'id')),
                SelectFilter::make('tags')
                    ->label('Tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
                RestoreAction::make(),
                ForceDeleteAction::make()->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
