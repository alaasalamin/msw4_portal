<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObjectRecordResource\Pages;
use App\Models\Customer;
use App\Models\ObjectRecord;
use App\Models\ObjectType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ObjectRecordResource extends Resource
{
    protected static ?string $model = ObjectRecord::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-squares-2x2'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Object Engine'; }
    public static function getNavigationSort(): ?int                    { return 2; }
    public static function getNavigationLabel(): string                 { return 'Records'; }
    public static function getModelLabel(): string                      { return 'Object Record'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Type & Customer')
                ->columns(2)
                ->schema([
                    Select::make('object_type_id')
                        ->label('Object type')
                        ->options(fn () => ObjectType::orderBy('name')->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->disabled(fn (string $context) => $context === 'edit')
                        ->helperText('Picking a type fills the form below with that type\'s attributes.'),

                    Select::make('customer_id')
                        ->label('Customer (optional)')
                        ->relationship('customer', 'name')
                        ->searchable(['name', 'email', 'company_name'])
                        ->preload(false)
                        ->nullable()
                        ->helperText('Leave blank if not sold/linked yet.'),
                ]),

            Section::make('Attributes')
                ->visible(fn (Get $get) => filled($get('object_type_id')))
                ->schema(fn (Get $get) => static::buildAttributeFields($get('object_type_id'))),
        ]);
    }

    /**
     * Build form components based on the selected ObjectType's attribute definitions.
     */
    protected static function buildAttributeFields(?int $typeId): array
    {
        if (! $typeId) return [];

        $type = ObjectType::find($typeId);
        if (! $type) return [];

        $components = [];
        foreach ($type->attributes ?? [] as $attr) {
            $key      = $attr['key']      ?? null;
            $label    = $attr['label']    ?? $key;
            $kind     = $attr['type']     ?? 'text';
            $required = $attr['required'] ?? false;

            if (! $key) continue;

            $statePath = "data.{$key}";

            $component = match ($kind) {
                'number'  => TextInput::make($statePath)->numeric(),
                'date'    => DatePicker::make($statePath),
                'boolean' => Toggle::make($statePath)->inline(false),
                'select'  => Select::make($statePath)->options(
                    collect($attr['options'] ?? [])->pluck('value', 'value')->all()
                )->searchable(),
                default   => TextInput::make($statePath)->maxLength(255),
            };

            $component
                ->label($label)
                ->required($required);

            $components[] = $component;
        }

        return $components;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('type.name')
                    ->label('Type')
                    ->icon(fn (ObjectRecord $record) => $record->type?->icon ?: 'heroicon-o-cube')
                    ->badge()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->formatStateUsing(fn ($state) => $state ?: '—')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('summary')
                    ->label('Summary')
                    ->state(fn (ObjectRecord $record): string => static::summarize($record))
                    ->wrap(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('object_type_id')
                    ->label('Type')
                    ->options(fn () => ObjectType::orderBy('name')->pluck('name', 'id')),
                SelectFilter::make('customer_id')
                    ->label('Customer linked')
                    ->options([
                        'with'    => 'Linked to a customer',
                        'without' => 'Not linked',
                    ])
                    ->query(function ($query, array $data) {
                        if (($data['value'] ?? null) === 'with')    $query->whereNotNull('customer_id');
                        if (($data['value'] ?? null) === 'without') $query->whereNull('customer_id');
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    /**
     * Compact one-line summary of the JSON data for the table.
     */
    private static function summarize(ObjectRecord $record): string
    {
        if (! is_array($record->data) || empty($record->data)) return '—';

        $parts = [];
        foreach ($record->data as $k => $v) {
            if ($v === null || $v === '') continue;
            $parts[] = "{$k}: " . (is_bool($v) ? ($v ? 'yes' : 'no') : (string) $v);
            if (count($parts) >= 4) break;
        }
        return $parts ? implode(' · ', $parts) : '—';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListObjectRecords::route('/'),
            'create' => Pages\CreateObjectRecord::route('/create'),
            'edit'   => Pages\EditObjectRecord::route('/{record}/edit'),
        ];
    }
}
