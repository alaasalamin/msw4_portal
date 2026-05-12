<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObjectRecordResource\Pages;
use App\Models\Customer;
use App\Models\ObjectRecord;
use App\Models\ObjectType;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
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
    public static function getModelLabel(): string                      { return 'Object Record'; }

    /**
     * Hidden from the sidebar — per-type entries are registered dynamically
     * in AdminPanelProvider so each ObjectType gets its own nav item.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

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
                'random'  => (function () use ($statePath, $attr, $type, $key) {
                    $length = max(6, min(64, (int) ($attr['length'] ?? 8)));
                    $typeId = $type->id;
                    $attrKey = $key;
                    return TextInput::make($statePath)
                        ->default(fn () => static::uniqueRandomId($length, $typeId, $attrKey))
                        ->dehydrated()
                        ->readOnly()
                        ->suffixAction(
                            Action::make('regen_' . md5($statePath))
                                ->icon('heroicon-o-arrow-path')
                                ->tooltip('Generate a new value')
                                ->action(fn (Set $set) => $set($statePath, static::uniqueRandomId($length, $typeId, $attrKey)))
                        );
                })(),
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
        $type = static::activeType();

        $columns = [
            TextColumn::make('id')->sortable(),
            TextColumn::make('type.name')
                ->label('Type')
                ->icon(fn (ObjectRecord $record) => $record->type?->icon ?: 'heroicon-o-cube')
                ->badge()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: $type !== null),
            TextColumn::make('customer.name')
                ->label('Customer')
                ->formatStateUsing(fn ($state) => $state ?: '—')
                ->searchable()
                ->toggleable(),
        ];

        // When viewing a single type, one column per attribute instead of a
        // crammed-together summary string.
        if ($type && is_array($type->attributes)) {
            foreach ($type->attributes as $attr) {
                $key   = $attr['key']   ?? null;
                $label = $attr['label'] ?? $key;
                $kind  = $attr['type']  ?? 'text';
                if (! $key) continue;

                $columns[] = TextColumn::make("data.{$key}")
                    ->label($label)
                    ->state(function (ObjectRecord $record) use ($key, $kind) {
                        $value = data_get($record->data, $key);
                        if ($value === null || $value === '') return null;
                        if ($kind === 'boolean') return $value ? 'Yes' : 'No';
                        return $value;
                    })
                    ->placeholder('—')
                    ->searchable(['data->'.$key])
                    ->toggleable();
            }
        }

        $columns[] = TextColumn::make('created_at')->dateTime()->sortable()->toggleable();

        return $table
            ->defaultSort('id', 'desc')
            ->columns($columns)
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
     * The ObjectType the list is currently filtered to (if any).
     * Reads from the table-filter URL the dynamic sidebar items use.
     */
    private static function activeType(): ?ObjectType
    {
        $id = (int) data_get(request()->query(), 'filters.object_type_id.value');
        return $id > 0 ? ObjectType::find($id) : null;
    }

    /**
     * Lowercase alphanumeric (a–z, 0–9) string of $length characters.
     */
    public static function randomId(int $length): string
    {
        // Str::random returns mixed-case alphanumeric; lowercasing gives a–z + 0–9.
        return strtolower(Str::random($length));
    }

    /**
     * Like randomId(), but guaranteed unique for the given attribute key within
     * an object type (e.g. no two "Used Phones" records share the same internal_id).
     */
    public static function uniqueRandomId(int $length, int $typeId, string $key): string
    {
        $length = max(6, $length);

        for ($attempt = 0; $attempt < 16; $attempt++) {
            $candidate = static::randomId($length);
            $clash = ObjectRecord::where('object_type_id', $typeId)
                ->where("data->{$key}", $candidate)
                ->exists();
            if (! $clash) return $candidate;
        }

        // 16 collisions in a row is astronomically unlikely — bump length and retry.
        return static::uniqueRandomId($length + 2, $typeId, $key);
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
