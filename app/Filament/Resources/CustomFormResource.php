<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomFormResource\Pages;
use App\Models\CustomForm;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Actions as FormActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CustomFormResource extends Resource
{
    protected static ?string $model = CustomForm::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-clipboard-document'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Content'; }
    public static function getNavigationSort(): ?int                    { return 2; }
    public static function getNavigationLabel(): string                 { return 'Forms'; }
    public static function getModelLabel(): string                      { return 'Form'; }
    public static function getPluralModelLabel(): string                { return 'Forms'; }

    public static function form(Schema $form): Schema
    {
        return $form->columns(2)->components([

            // ── Left: Form Builder ────────────────────────────────────────────
            Section::make('Form Fields')
                ->description('Build your form by adding fields, spacers, and headings.')
                ->schema([
                    Repeater::make('fields')
                        ->label('')
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->addActionLabel('+ Add field')
                        ->itemLabel(function (array $state): string {
                            $type  = $state['type'] ?? 'text';
                            $label = $state['label'] ?? '';

                            return match ($type) {
                                'spacer'     => '— Horizontal Divider',
                                'subheading' => '# ' . ($label ?: 'Sub Heading'),
                                default      => ($label ?: 'New field')
                                    . '  ·  ' . strtoupper($type)
                                    . (($state['is_required'] ?? false) ? '  *' : ''),
                            };
                        })
                        ->collapsible()
                        ->collapsed()
                        ->grid(2)
                        ->live()
                        ->schema([
                            TextInput::make('label')
                                ->label(fn (Get $get) => $get('type') === 'subheading' ? 'Heading Text' : 'Label')
                                ->required(fn (Get $get) => $get('type') !== 'spacer')
                                ->maxLength(100)
                                ->live(onBlur: true)
                                ->visible(fn (Get $get) => $get('type') !== 'spacer'),

                            Select::make('type')
                                ->options([
                                    'Input fields' => [
                                        'text'     => 'Text',
                                        'email'    => 'Email',
                                        'tel'      => 'Phone number',
                                        'number'   => 'Number',
                                        'textarea' => 'Long text (Textarea)',
                                        'select'   => 'Dropdown',
                                        'checkbox' => 'Checkbox',
                                    ],
                                    'Layout' => [
                                        'subheading' => 'Sub Heading',
                                        'spacer'     => 'Spacer / Divider',
                                    ],
                                ])
                                ->default('text')
                                ->required()
                                ->live(),

                            TextInput::make('placeholder')
                                ->label(fn (Get $get) => $get('type') === 'subheading' ? 'Subtitle (optional)' : 'Placeholder')
                                ->maxLength(150)
                                ->visible(fn (Get $get) => ! in_array($get('type'), ['checkbox', 'spacer'])),

                            Select::make('col_span')
                                ->label('Width')
                                ->options([
                                    'full' => 'Full width',
                                    'half' => 'Half width',
                                ])
                                ->default('full')
                                ->required()
                                ->visible(fn (Get $get) => ! in_array($get('type'), ['spacer', 'subheading'])),

                            Toggle::make('is_required')
                                ->label('Required')
                                ->inline(false)
                                ->visible(fn (Get $get) => ! in_array($get('type'), ['spacer', 'subheading'])),

                            Repeater::make('options')
                                ->label('Dropdown options')
                                ->schema([
                                    TextInput::make('label')->required()->maxLength(80),
                                    TextInput::make('value')->required()->maxLength(80),
                                ])
                                ->columns(2)
                                ->addActionLabel('+ Add option')
                                ->defaultItems(0)
                                ->visible(fn (Get $get) => $get('type') === 'select')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),

                    FormActions::make([
                        Action::make('customer_package')
                            ->label('Customer Package')
                            ->icon('heroicon-o-user')
                            ->color('primary')
                            ->tooltip('Adds Name, Email, Phone and full address fields in one click')
                            ->action('addCustomerPackage'),
                        Action::make('add_subheading')
                            ->label('Sub Heading')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->color('gray')
                            ->tooltip('Insert a section title to group fields visually')
                            ->action('addSubHeading'),
                        Action::make('add_spacer')
                            ->label('Spacer')
                            ->icon('heroicon-o-minus')
                            ->color('gray')
                            ->tooltip('Insert a horizontal divider line between fields')
                            ->action('addSpacer'),
                    ])->columnSpanFull(),
                ])
                ->columnSpan(1),

            // ── Right: Live Preview ───────────────────────────────────────────
            Section::make('Preview')
                ->description('Live preview of how your form will look to visitors.')
                ->schema([
                    Placeholder::make('form_preview')
                        ->label('')
                        ->content(fn (Get $get): HtmlString => new HtmlString(
                            static::renderPreview($get('fields') ?? [])
                        )),
                ])
                ->columnSpan(1),

            // ── Below: Form Details ───────────────────────────────────────────
            Section::make('Form Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(120)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', \Illuminate\Support\Str::slug($state))
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Auto-generated from name. Used in URLs.'),

                    Textarea::make('description')
                        ->rows(2)
                        ->columnSpanFull(),

                    Textarea::make('success_message')
                        ->label('Success message')
                        ->helperText('Shown to the user after they submit.')
                        ->rows(2)
                        ->placeholder('Thank you! We will get back to you shortly.'),

                    TextInput::make('redirect_url')
                        ->label('Redirect URL (optional)')
                        ->helperText('Send the user to this page after submit.')
                        ->placeholder('/thank-you'),
                ])
                ->columns(2)
                ->columnSpanFull(),

            // ── Below: CRM Integration ────────────────────────────────────────
            Section::make('CRM Integration')
                ->description('When enabled, every new submission is automatically sent to the CRM.')
                ->schema([
                    Toggle::make('crm_sync')
                        ->label('Send submissions to CRM')
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            if ($state && blank($get('crm_key'))) {
                                $set('crm_key', bin2hex(random_bytes(16)));
                            }
                        }),

                    TextInput::make('crm_key')
                        ->label('CRM Key')
                        ->helperText('Auto-generated. The CRM creates a table named after this form\'s slug.')
                        ->readOnly()
                        ->visible(fn (Get $get) => (bool) $get('crm_sync'))
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->collapsible()
                ->columnSpanFull(),

            // ── Below: Preset Replies ─────────────────────────────────────────
            Section::make('Preset Replies')
                ->description('Saved reply templates. Use {{Field Label}} to insert submission values.')
                ->schema([
                    Repeater::make('preset_replies')
                        ->label('')
                        ->addActionLabel('+ Add preset reply')
                        ->itemLabel(fn (array $state): string => $state['name'] ?? 'Unnamed preset')
                        ->collapsible()
                        ->collapsed()
                        ->defaultItems(0)
                        ->schema([
                            TextInput::make('name')
                                ->label('Preset name')
                                ->placeholder('e.g. Thank you, Price info, Follow-up…')
                                ->required()
                                ->maxLength(80)
                                ->live(onBlur: true),

                            TextInput::make('subject')
                                ->label('Email subject')
                                ->placeholder('e.g. Re: Your enquiry — {{Name}}')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Textarea::make('body')
                                ->label('Message body')
                                ->placeholder("Dear {{Name}},\n\nThank you for reaching out…")
                                ->required()
                                ->rows(6)
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->columnSpanFull(),

        ]);
    }

    private static function renderPreview(array $fields): string
    {
        $fields = array_values($fields);

        if (empty($fields)) {
            return '<div style="padding:40px 16px;text-align:center;color:#9ca3af;font-size:13px;font-family:Inter,sans-serif;">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:32px;height:32px;margin:0 auto 8px;display:block;opacity:.4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Add fields to see a live preview
                    </div>';
        }

        $rows  = '';
        $input = 'width:100%;padding:7px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#6b7280;background:#fff;box-sizing:border-box;';

        foreach ($fields as $f) {
            $type    = $f['type']        ?? 'text';
            $label   = htmlspecialchars($f['label']       ?? '', ENT_QUOTES);
            $ph      = htmlspecialchars($f['placeholder'] ?? '', ENT_QUOTES);
            $req     = ! empty($f['is_required']);
            $span    = ($f['col_span'] ?? 'full') === 'half' ? '1' : '2';

            if ($type === 'spacer') {
                $rows .= '<div style="grid-column:span 2;padding:4px 0;"><hr style="border:none;border-top:1px solid #e5e7eb;"></div>';
                continue;
            }

            if ($type === 'subheading') {
                $sub = htmlspecialchars($f['placeholder'] ?? '', ENT_QUOTES);
                $rows .= '<div style="grid-column:span 2;">'
                    . '<p style="font-size:13px;font-weight:600;color:#111827;margin:0 0 2px;">' . ($label ?: 'Sub Heading') . '</p>'
                    . ($sub ? '<p style="font-size:11px;color:#6b7280;margin:0;">' . $sub . '</p>' : '')
                    . '</div>';
                continue;
            }

            $star   = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            $lbl    = '<label style="display:block;font-size:11px;font-weight:500;color:#374151;margin-bottom:3px;">' . ($label ?: '—') . $star . '</label>';

            if ($type === 'textarea') {
                $ctrl = '<textarea style="' . $input . 'resize:none;" rows="2" disabled placeholder="' . $ph . '"></textarea>';
            } elseif ($type === 'select') {
                $ctrl = '<select style="' . $input . '" disabled><option>' . ($ph ?: '— select —') . '</option></select>';
            } elseif ($type === 'checkbox') {
                $ctrl = '<div style="display:flex;align-items:center;gap:7px;"><input type="checkbox" disabled style="width:13px;height:13px;accent-color:#f97316;"><span style="font-size:12px;color:#6b7280;">' . ($ph ?: $label) . '</span></div>';
            } else {
                $ctrl = '<input type="' . htmlspecialchars($type, ENT_QUOTES) . '" style="' . $input . '" disabled placeholder="' . $ph . '">';
            }

            $rows .= '<div style="grid-column:span ' . $span . ';">' . $lbl . $ctrl . '</div>';
        }

        return '<div style="font-family:Inter,sans-serif;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:20px;">'
            . '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">' . $rows . '</div>'
            . '<button disabled style="margin-top:14px;width:100%;padding:9px;background:#f97316;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;opacity:.85;cursor:not-allowed;">Send</button>'
            . '</div>';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('slug')->color('gray'),
                TextColumn::make('fields_count')
                    ->label('Fields')
                    ->counts('fields')
                    ->badge()->color('info'),
                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions')
                    ->badge()->color('success'),
                TextColumn::make('updated_at')->label('Updated')->since()->sortable(),
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
            'index'  => Pages\ListCustomForms::route('/'),
            'create' => Pages\CreateCustomForm::route('/create'),
            'edit'   => Pages\EditCustomForm::route('/{record}/edit'),
        ];
    }
}
