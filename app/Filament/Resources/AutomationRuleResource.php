<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutomationRuleResource\Pages;
use App\Models\AutomationAction;
use App\Models\AutomationRule;
use App\Models\WorkflowStep;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AutomationRuleResource extends Resource
{
    protected static ?string $model = AutomationRule::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-bolt'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Workflow'; }
    public static function getNavigationSort(): ?int                    { return 5; }
    public static function getNavigationLabel(): string                 { return 'Automationen'; }
    public static function getModelLabel(): string                      { return 'Automation'; }
    public static function getPluralModelLabel(): string                { return 'Automationen'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([

            Section::make('Regel')->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(120)
                    ->placeholder('z.B. KV → Kundenfreigabe anfordern'),

                Textarea::make('description')
                    ->label('Beschreibung')
                    ->rows(2)
                    ->placeholder('Was macht diese Automation?'),

                Toggle::make('is_active')
                    ->label('Aktiv')
                    ->default(true)
                    ->inline(false),

                TextInput::make('sort_order')
                    ->label('Reihenfolge')
                    ->numeric()
                    ->default(0),
            ])->columns(2),

            Section::make('Auslöser (WENN)')->schema([
                Select::make('trigger_type')
                    ->label('Auslöser')
                    ->options(AutomationRule::triggerLabels())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('trigger_config', [])),

                // step_changed config
                Select::make('trigger_config.step_id')
                    ->label('Bei welchem Schritt?')
                    ->placeholder('Beliebiger Schritt')
                    ->options(fn () => WorkflowStep::with('phase')
                        ->get()
                        ->groupBy('phase.label')
                        ->map(fn ($steps) => $steps->pluck('label', 'id'))
                        ->toArray()
                    )
                    ->nullable()
                    ->visible(fn (Get $get) => $get('trigger_type') === 'step_changed'),
            ])->columns(2),

            Section::make('Aktionen (DANN)')->schema([
                Repeater::make('actions')
                    ->label('Aktionen (werden der Reihe nach ausgeführt)')
                    ->relationship()
                    ->orderColumn('sort_order')
                    ->reorderable()
                    ->collapsible()
                    ->addActionLabel('Aktion hinzufügen')
                    ->schema([
                        Select::make('action_type')
                            ->label('Aktion')
                            ->options(AutomationAction::actionLabels())
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        // ── send_allowance ──────────────────────────────────
                        Textarea::make('action_config.message')
                            ->label('Nachricht an Kunden')
                            ->rows(3)
                            ->placeholder('Sehr geehrter Kunde, wir bitten um Ihre Freigabe...')
                            ->visible(fn (Get $get) => $get('action_type') === 'send_allowance'),

                        TextInput::make('action_config.expires_days')
                            ->label('Gültig für (Tage)')
                            ->numeric()
                            ->default(7)
                            ->visible(fn (Get $get) => $get('action_type') === 'send_allowance'),

                        // ── notify_employee ─────────────────────────────────
                        Select::make('action_config.employee_ids')
                            ->label('Mitarbeiter (leer = alle)')
                            ->multiple()
                            ->options(fn () => User::where('type', 'employee')->pluck('name', 'id'))
                            ->visible(fn (Get $get) => $get('action_type') === 'notify_employee'),

                        Textarea::make('action_config.message')
                            ->label('Nachricht')
                            ->rows(2)
                            ->placeholder('Gerät {{ticket}} erfordert deine Aufmerksamkeit.')
                            ->visible(fn (Get $get) => $get('action_type') === 'notify_employee'),

                        // ── send_email ──────────────────────────────────────
                        Select::make('action_config.recipient')
                            ->label('Empfänger')
                            ->options(['customer' => 'Kunde', 'custom' => 'Benutzerdefiniert'])
                            ->default('customer')
                            ->live()
                            ->visible(fn (Get $get) => $get('action_type') === 'send_email'),

                        TextInput::make('action_config.custom_email')
                            ->label('E-Mail-Adresse')
                            ->email()
                            ->visible(fn (Get $get) => $get('action_type') === 'send_email'
                                && $get('action_config.recipient') === 'custom'),

                        TextInput::make('action_config.subject')
                            ->label('Betreff')
                            ->placeholder('Gerät {{ticket}} — Update')
                            ->visible(fn (Get $get) => $get('action_type') === 'send_email'),

                        Textarea::make('action_config.body')
                            ->label('Inhalt')
                            ->rows(4)
                            ->placeholder("Verfügbare Variablen: {{ticket}}, {{brand}}, {{model}}, {{customer}}")
                            ->visible(fn (Get $get) => $get('action_type') === 'send_email'),

                        // ── send_delayed_email ──────────────────────────────
                        TextInput::make('action_config.delay_value')
                            ->label('Verzögerung')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->visible(fn (Get $get) => $get('action_type') === 'send_delayed_email'),

                        Select::make('action_config.delay_unit')
                            ->label('Einheit')
                            ->options(['minutes' => 'Minuten', 'hours' => 'Stunden', 'days' => 'Tage'])
                            ->default('hours')
                            ->live()
                            ->visible(fn (Get $get) => $get('action_type') === 'send_delayed_email'),

                        Select::make('action_config.recipient')
                            ->label('Empfänger')
                            ->options(['customer' => 'Kunde', 'custom' => 'Benutzerdefiniert'])
                            ->default('customer')
                            ->live()
                            ->visible(fn (Get $get) => $get('action_type') === 'send_delayed_email'),

                        TextInput::make('action_config.custom_email')
                            ->label('E-Mail-Adresse')
                            ->email()
                            ->visible(fn (Get $get) => $get('action_type') === 'send_delayed_email'
                                && $get('action_config.recipient') === 'custom'),

                        TextInput::make('action_config.subject')
                            ->label('Betreff')
                            ->default('Dein Gerät {{ticket}} ist abholbereit')
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('action_type') === 'send_delayed_email'),

                        Textarea::make('action_config.body')
                            ->label('E-Mail-Text')
                            ->rows(6)
                            ->default("Hallo {{customer}},\n\ndein {{brand}} {{model}} (Ticket {{ticket}}) ist fertig und kann abgeholt werden.\n\nBis bald,\nDas MSW-Team")
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('action_type') === 'send_delayed_email'),

                        // ── change_step ─────────────────────────────────────
                        Select::make('action_config.step_id')
                            ->label('Zielschritt')
                            ->options(fn () => WorkflowStep::with('phase')
                                ->get()
                                ->groupBy('phase.label')
                                ->map(fn ($steps) => $steps->pluck('label', 'id'))
                                ->toArray()
                            )
                            ->required(fn (Get $get) => $get('action_type') === 'change_step')
                            ->visible(fn (Get $get) => $get('action_type') === 'change_step'),

                        // ── generate_invoice ────────────────────────────────
                        TextInput::make('action_config.template')
                            ->label('RSW-Vorlage (optional)')
                            ->placeholder('standard')
                            ->visible(fn (Get $get) => $get('action_type') === 'generate_invoice'),
                    ])
                    ->columns(2),
            ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_active')
                    ->label('')
                    ->boolean()
                    ->width('40px'),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('trigger_type')
                    ->label('Auslöser')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => AutomationRule::triggerLabels()[$state] ?? $state),

                TextColumn::make('actions_count')
                    ->label('Aktionen')
                    ->counts('actions')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('logs_count')
                    ->label('Ausführungen')
                    ->counts('logs')
                    ->badge()
                    ->color('success'),

                TextColumn::make('updated_at')
                    ->label('Geändert')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Action::make('toggle')
                    ->label(fn (AutomationRule $record) => $record->is_active ? 'Deaktivieren' : 'Aktivieren')
                    ->icon(fn (AutomationRule $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (AutomationRule $record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn (AutomationRule $record) => $record->update(['is_active' => ! $record->is_active])),

                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAutomationRules::route('/'),
            'create' => Pages\CreateAutomationRule::route('/create'),
            'edit'   => Pages\EditAutomationRule::route('/{record}/edit'),
        ];
    }
}
