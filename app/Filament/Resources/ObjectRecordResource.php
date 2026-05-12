<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObjectRecordResource\Pages;
use App\Models\EngineRecord;
use App\Models\ObjectType;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ObjectRecordResource extends Resource
{
    protected static ?string $model = EngineRecord::class;

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

    /**
     * Slug of the active type. Pages set this from their `?type=<slug>` Livewire
     * URL property so the value survives Livewire updates (which POST to
     * /livewire/update and don't carry the page's query string).
     */
    public static ?string $currentTypeSlug = null;

    /**
     * Resolve the active ObjectType.
     *
     * Pages set $currentTypeSlug in boot() / mount(); fall back to the request
     * query for full-page GETs where that hook hasn't run yet.
     */
    public static function activeType(): ?ObjectType
    {
        static $cache = [];

        $slug = static::$currentTypeSlug ?: (string) request()->query('type');
        if ($slug === '') return null;
        if (array_key_exists($slug, $cache)) return $cache[$slug];

        return $cache[$slug] = ObjectType::where('slug', $slug)->first();
    }

    /**
     * Drive the resource's Eloquent model off the active type's table.
     */
    public static function getEloquentQuery(): Builder
    {
        $type = static::activeType();
        if (! $type) {
            // No type in URL → return a query that yields nothing rather than crash.
            return EngineRecord::query()->whereRaw('1 = 0');
        }

        return EngineRecord::forType($type)->newQuery();
    }

    public static function resolveRecordRouteBinding(int | string $key, ?Closure $modifyQuery = null): ?Model
    {
        $type = static::activeType();

        if ($type) {
            $query = EngineRecord::forType($type)->newQuery();
            if ($modifyQuery) $query = $modifyQuery($query);
            return $query->find($key);
        }

        // No type hint in URL — scan each engine_<slug> table for a record with this id.
        foreach (ObjectType::orderBy('id')->get() as $candidate) {
            if (! \Illuminate\Support\Facades\Schema::hasTable($candidate->engineTable())) continue;
            $query = EngineRecord::forType($candidate)->newQuery();
            if ($modifyQuery) $query = $modifyQuery($query);
            $record = $query->find($key);
            if ($record) {
                static::$currentTypeSlug = $candidate->slug;
                return $record;
            }
        }

        return null;
    }

    public static function form(Schema $form): Schema
    {
        $type = static::activeType();

        $components = [];

        $components[] = Select::make('customer_id')
            ->label('Customer (optional)')
            ->relationship('customer', 'name')
            ->searchable(['name', 'email', 'company_name'])
            ->preload(false)
            ->nullable()
            ->helperText('Leave blank if not sold/linked yet.');

        if ($type && is_array($type->attributes)) {
            foreach ($type->attributes as $attr) {
                $field = static::buildAttributeField($type, $attr);
                if ($field) $components[] = $field;
            }
        }

        return $form->components([
            Section::make($type?->name ?? 'Record')
                ->columns(2)
                ->schema($components),
        ]);
    }

    /**
     * Build one Filament form component for a single attribute definition.
     */
    private static function buildAttributeField(ObjectType $type, array $attr)
    {
        $key      = $attr['key']      ?? null;
        $label    = $attr['label']    ?? $key;
        $kind     = $attr['type']     ?? 'text';
        $required = $attr['required'] ?? false;

        if (! $key) return null;

        $component = match ($kind) {
            'number'  => TextInput::make($key)->numeric(),
            'date'    => DatePicker::make($key),
            'boolean' => Toggle::make($key)->inline(false),
            'select'  => Select::make($key)->options(
                collect($attr['options'] ?? [])->pluck('value', 'value')->all()
            )->searchable(),
            'random'  => (function () use ($key, $attr, $type) {
                $length = max(6, min(64, (int) ($attr['length'] ?? 8)));
                $typeId = $type->id;
                $attrKey = $key;
                return TextInput::make($key)
                    ->default(fn () => static::uniqueRandomId($length, $type, $attrKey))
                    ->dehydrated()
                    ->readOnly()
                    ->suffixAction(
                        Action::make('regen_' . md5($key))
                            ->icon('heroicon-o-arrow-path')
                            ->tooltip('Generate a new value')
                            ->action(fn (Set $set) => $set($key, static::uniqueRandomId($length, $type, $attrKey)))
                    );
            })(),
            default   => TextInput::make($key)->maxLength(255),
        };

        return $component->label($label)->required($required);
    }

    public static function table(Table $table): Table
    {
        $type = static::activeType();

        $columns = [
            TextColumn::make('id')->sortable(),
            TextColumn::make('customer.name')
                ->label('Customer')
                ->formatStateUsing(fn ($state) => $state ?: '—')
                ->searchable()
                ->toggleable(),
        ];

        if ($type && is_array($type->attributes)) {
            foreach ($type->attributes as $attr) {
                $key   = $attr['key']   ?? null;
                $label = $attr['label'] ?? $key;
                $kind  = $attr['type']  ?? 'text';
                if (! $key) continue;

                $columns[] = TextColumn::make($key)
                    ->label($label)
                    ->state(function (EngineRecord $record) use ($key, $kind) {
                        $value = $record->{$key} ?? null;
                        if ($value === null || $value === '') return null;
                        if ($kind === 'boolean') return $value ? 'Yes' : 'No';
                        return $value;
                    })
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable();
            }
        }

        $columns[] = TextColumn::make('created_at')->dateTime()->sortable()->toggleable();

        return $table
            ->defaultSort('id', 'desc')
            ->columns($columns)
            ->actions([
                EditAction::make()
                    ->url(fn (EngineRecord $record) => static::getUrl('edit', [
                        'record' => $record->id,
                        'type'   => static::activeType()?->slug,
                    ])),
                DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    public static function randomId(int $length): string
    {
        return strtolower(Str::random($length));
    }

    public static function uniqueRandomId(int $length, ObjectType $type, string $key): string
    {
        $length = max(6, $length);
        $table = $type->engineTable();

        for ($attempt = 0; $attempt < 16; $attempt++) {
            $candidate = static::randomId($length);
            $clash = \DB::table($table)->where($key, $candidate)->exists();
            if (! $clash) return $candidate;
        }

        return static::uniqueRandomId($length + 2, $type, $key);
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
