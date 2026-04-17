<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomFormResource\Pages;
use App\Models\CustomForm;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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

            // ── Left: Form Fields ────────────────────────────────────────────
            Section::make('Form Fields')
                ->description('Define the fields that will appear in this form.')
                ->schema([
                    Repeater::make('fields')
                        ->label('')
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->reorderable()
                        ->addActionLabel('+ Add field')
                        ->itemLabel(fn (array $state): string =>
                            ($state['label'] ?? 'New field') . ' (' . ($state['type'] ?? 'text') . ')' .
                            (($state['is_required'] ?? false) ? ' *' : '')
                        )
                        ->collapsible()
                        ->collapsed()
                        ->schema([
                            TextInput::make('label')
                                ->required()
                                ->maxLength(100)
                                ->live(onBlur: true),

                            Select::make('type')
                                ->options([
                                    'text'     => 'Text',
                                    'email'    => 'Email',
                                    'tel'      => 'Phone number',
                                    'number'   => 'Number',
                                    'textarea' => 'Textarea (long text)',
                                    'select'   => 'Dropdown / Select',
                                    'checkbox' => 'Checkbox',
                                ])
                                ->default('text')
                                ->required()
                                ->live(),

                            TextInput::make('placeholder')
                                ->maxLength(150)
                                ->visible(fn (Get $get) => ! in_array($get('type'), ['checkbox'])),

                            Toggle::make('is_required')
                                ->label('Required')
                                ->inline(false),

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
                ])
                ->columnSpan(1),

            // ── Right: Form Details ──────────────────────────────────────────
            Section::make('Form Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(120)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', \Illuminate\Support\Str::slug($state))
                        )
                        ->columnSpanFull(),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Auto-generated. Used internally.')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->rows(2)
                        ->columnSpanFull(),

                    Textarea::make('success_message')
                        ->label('Success message (shown after submit)')
                        ->rows(2)
                        ->placeholder('Thank you! We will get back to you shortly.')
                        ->columnSpanFull(),

                    TextInput::make('redirect_url')
                        ->label('Redirect URL after submit (optional)')
                        ->placeholder('/thank-you')
                        ->columnSpanFull(),
                ])
                ->columnSpan(1),

            // ── Bottom: Preset Replies ───────────────────────────────────────
            Section::make('Preset Replies')
                ->description('Save reply templates for this form. Use {{Field Label}} to insert submission values — e.g. {{Name}}, {{Email}}.')
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
