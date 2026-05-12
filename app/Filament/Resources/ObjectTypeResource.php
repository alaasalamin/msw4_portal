<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObjectTypeResource\Pages;
use App\Models\ObjectType;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ObjectTypeResource extends Resource
{
    protected static ?string $model = ObjectType::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-rectangle-stack'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Object Engine'; }
    public static function getNavigationSort(): ?int                    { return 1; }
    public static function getNavigationLabel(): string                 { return 'Object Types'; }
    public static function getModelLabel(): string                      { return 'Object Type'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->helperText('Anything — e.g. "Used Phones", "Spare Parts", "Tickets".'),

            Repeater::make('attributes')
                ->label('Attributes')
                ->helperText('Add as many fields as you want. Each becomes an input on the record form.')
                ->columnSpanFull()
                ->defaultItems(1)
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => filled($state['label'] ?? null) ? $state['label'] : ($state['key'] ?? null))
                ->schema([
                    TextInput::make('key')
                        ->required()
                        ->maxLength(64)
                        ->regex('/^[a-z][a-z0-9_]*$/')
                        ->helperText('snake_case identifier — e.g. "imei", "purchase_date".'),
                    TextInput::make('label')
                        ->required()
                        ->maxLength(120)
                        ->helperText('Human-readable label shown on the form.'),
                    Select::make('type')
                        ->options([
                            'text'    => 'Text',
                            'number'  => 'Number',
                            'date'    => 'Date',
                            'select'  => 'Select (dropdown)',
                            'boolean' => 'Boolean (toggle)',
                        ])
                        ->required()
                        ->live()
                        ->default('text'),
                    Repeater::make('options')
                        ->label('Options')
                        ->helperText('One option per row.')
                        ->schema([
                            TextInput::make('value')->required()->maxLength(120),
                        ])
                        ->visible(fn (Get $get) => $get('type') === 'select')
                        ->defaultItems(1)
                        ->columnSpanFull(),
                    Toggle::make('required')->inline(false)->default(false),
                ])
                ->columns(2)
                ->addActionLabel('Add attribute'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->toggleable(),
                TextColumn::make('attributes')
                    ->label('Fields')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) : 0),
                TextColumn::make('records_count')
                    ->counts('records')
                    ->label('Records')
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListObjectTypes::route('/'),
            'create' => Pages\CreateObjectType::route('/create'),
            'edit'   => Pages\EditObjectType::route('/{record}/edit'),
        ];
    }
}
