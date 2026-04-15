<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkflowPhaseResource\Pages;
use App\Models\WorkflowPhase;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkflowPhaseResource extends Resource
{
    protected static ?string $model = WorkflowPhase::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-flag'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Workflow'; }
    public static function getNavigationSort(): ?int                    { return 1; }
    public static function getNavigationLabel(): string                 { return 'Phasen'; }
    public static function getModelLabel(): string                      { return 'Phase'; }
    public static function getPluralModelLabel(): string                { return 'Phasen'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->schema([
                TextInput::make('label')
                    ->label('Bezeichnung')
                    ->required()
                    ->maxLength(120)
                    ->placeholder('z.B. Phase 1: Diagnose & Vorbereitung'),

                TextInput::make('sort_order')
                    ->label('Reihenfolge')
                    ->numeric()
                    ->default(0)
                    ->helperText('Niedrigere Zahl = früher angezeigt'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('48px'),

                TextColumn::make('label')
                    ->label('Bezeichnung')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('steps_count')
                    ->label('Schritte')
                    ->counts('steps')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Geändert')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->disabled(fn (WorkflowPhase $record) => $record->steps()->exists()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkflowPhases::route('/'),
            'create' => Pages\CreateWorkflowPhase::route('/create'),
            'edit'   => Pages\EditWorkflowPhase::route('/{record}/edit'),
        ];
    }
}
