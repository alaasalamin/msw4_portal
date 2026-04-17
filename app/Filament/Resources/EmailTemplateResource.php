<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-envelope-open'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Content'; }
    public static function getNavigationSort(): ?int                    { return 5; }
    public static function getNavigationLabel(): string                 { return 'Auto-Response Emails'; }
    public static function getModelLabel(): string                      { return 'Email Template'; }
    public static function getPluralModelLabel(): string                { return 'Email Templates'; }

    /** Device model variables available as placeholders */
    private static array $variables = [
        '{{ticket_number}}'        => 'Ticket number (e.g. REP-2026-0042)',
        '{{customer_name}}'        => 'Customer full name',
        '{{customer_email}}'       => 'Customer email address',
        '{{customer_phone}}'       => 'Customer phone number',
        '{{brand}}'                => 'Device brand (e.g. Apple)',
        '{{model}}'                => 'Device model (e.g. iPhone 15 Pro)',
        '{{serial_number}}'        => 'Serial number',
        '{{color}}'                => 'Device color',
        '{{storage_box}}'          => 'Storage box location',
        '{{issue_description}}'    => 'Fehler / problem description',
        '{{internal_notes}}'       => 'Internal notes',
        '{{priority}}'             => 'Priority level',
        '{{estimated_cost}}'       => 'Estimated repair cost',
        '{{final_cost}}'           => 'Final cost',
        '{{received_at}}'          => 'Date received (dd.mm.yyyy)',
        '{{estimated_completion}}' => 'Estimated completion date',
        '{{completed_at}}'         => 'Date completed',
        '{{workflow_step}}'        => 'Current workflow step label',
    ];

    public static function form(Schema $form): Schema
    {
        $varRows = collect(static::$variables)
            ->map(fn ($desc, $var) => "  {$var}  —  {$desc}")
            ->implode("\n");

        return $form->columns(2)->components([

            // ── Left: template editor ────────────────────────────────────────
            Section::make('Template')
                ->schema([
                    TextInput::make('name')
                        ->label('Template name')
                        ->placeholder('e.g. Repair ready, Waiting for parts…')
                        ->required()
                        ->maxLength(120)
                        ->columnSpanFull(),

                    TextInput::make('subject')
                        ->label('Email subject')
                        ->placeholder('e.g. Your repair {{ticket_number}} is ready — {{brand}} {{model}}')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make('body')
                        ->label('Email body')
                        ->placeholder("Dear {{customer_name}},\n\nYour {{brand}} {{model}} (Ticket: {{ticket_number}}) is ready for pickup.\n\nIssue reported: {{issue_description}}\n\nBest regards,\nMSW Repair Team")
                        ->required()
                        ->rows(14)
                        ->columnSpanFull(),
                ])
                ->columnSpan(1),

            // ── Right: available variables reference ─────────────────────────
            Section::make('Available variables')
                ->description('Click any variable to copy it, then paste it into the subject or body.')
                ->schema([
                    Placeholder::make('variables_reference')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString(
                            self::buildVariableChips()
                        )),
                ])
                ->columnSpan(1),
        ]);
    }

    private static function buildVariableChips(): string
    {
        $chips = '';
        foreach (static::$variables as $var => $desc) {
            $chips .= sprintf(
                '<div style="margin-bottom:8px;">'
                . '<button type="button" onclick="navigator.clipboard.writeText(\'%s\');this.textContent=\'Copied!\';setTimeout(()=>this.textContent=\'%s\',1200);" '
                . 'style="font-family:monospace;font-size:12px;font-weight:600;color:#6366f1;background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);border-radius:6px;padding:3px 8px;cursor:pointer;transition:all .15s;">%s</button>'
                . '<span style="font-size:11px;color:#6b7280;margin-left:8px;">%s</span>'
                . '</div>',
                $var, $var, $var, $desc
            );
        }
        return $chips;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('subject')
                    ->limit(60)
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit'   => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
