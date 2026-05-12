<?php

namespace App\Filament\Resources\ObjectTypeResource\Pages;

use App\Filament\Resources\ObjectTypeResource;
use App\Models\ObjectType;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\HtmlString;

class CreateObjectType extends CreateRecord
{
    use HasWizard;

    protected static string $resource = ObjectTypeResource::class;

    public function getTitle(): string
    {
        return 'New Object Engine';
    }

    public function getHeading(): string
    {
        return $this->getTitle();
    }

    protected function getSteps(): array
    {
        return [
            Step::make('Name')
                ->description('What is this object?')
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->autofocus()
                        ->placeholder('e.g. Devices, Insurance')
                        ->helperText('This becomes the label in the sidebar and the page title.'),
                ]),

            Step::make('Icon')
                ->description('Pick a symbol')
                ->icon('heroicon-o-squares-2x2')
                ->schema([
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
                ]),

            Step::make('Attributes')
                ->description('Add the fields each record will have')
                ->icon('heroicon-o-list-bullet')
                ->schema([
                    Repeater::make('attributes')
                        ->label('Attributes')
                        ->helperText('Each becomes an input on the record form (and a real column on the type\'s table).')
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
                                ->helperText('Lowercase a–z and 0–9. Auto-generated values stay unique within this object type.'),
                            Toggle::make('required')->inline(false)->default(false),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add attribute'),
                ]),
        ];
    }
}
