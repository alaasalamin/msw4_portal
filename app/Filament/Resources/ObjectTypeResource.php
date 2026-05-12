<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObjectTypeResource\Pages;
use App\Models\ObjectType;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ObjectTypeResource extends Resource
{
    protected static ?string $model = ObjectType::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-rectangle-stack'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Object Engine'; }
    public static function getNavigationSort(): ?int                    { return 1; }
    public static function getNavigationLabel(): string                 { return 'Object Engine'; }
    public static function getModelLabel(): string                      { return 'Object Engine'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->helperText('Anything — e.g. "Used Phones", "Spare Parts", "Tickets".'),

            Hidden::make('icon')
                ->default('heroicon-o-cube')
                ->required(),

            Actions::make([
                Action::make('pickIcon')
                    ->label(function (Get $get): HtmlString {
                        $icon = $get('icon') ?: 'heroicon-o-cube';
                        return new HtmlString(view('filament.forms.icon-picker-trigger', [
                            'icon'  => $icon,
                            'label' => 'Icon',
                        ])->render());
                    })
                    ->modalHeading('Choose an icon')
                    ->modalWidth('2xl')
                    ->modalContent(fn () => view('filament.forms.icon-picker-grid'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->color('gray')
                    ->extraAttributes([
                        'style' => 'min-width:120px; height:auto; padding:8px 12px;',
                    ]),
            ]),

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
                            'random'  => 'Random ID (auto-generated)',
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
                    TextInput::make('length')
                        ->numeric()
                        ->minValue(6)
                        ->maxValue(32)
                        ->default(8)
                        ->visible(fn (Get $get) => $get('type') === 'random')
                        ->helperText('How many characters. Uses lowercase a–z and 0–9. Auto-generated values stay unique within this object type.'),
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
                TextColumn::make('name')
                    ->icon(fn (ObjectType $record) => $record->icon ?: 'heroicon-o-cube')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')->toggleable(),
                TextColumn::make('attributes_count')
                    ->label('Fields')
                    ->state(fn (ObjectType $record): int => is_array($record->attributes) ? count($record->attributes) : 0),
                TextColumn::make('records_count')
                    ->label('Records')
                    ->state(function (ObjectType $record): int {
                        $table = $record->engineTable();
                        return \Illuminate\Support\Facades\Schema::hasTable($table)
                            ? (int) \Illuminate\Support\Facades\DB::table($table)->count()
                            : 0;
                    }),
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
