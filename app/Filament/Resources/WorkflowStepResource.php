<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkflowStepResource\Pages;
use App\Models\WorkflowPhase;
use App\Models\WorkflowStep;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkflowStepResource extends Resource
{
    protected static ?string $model = WorkflowStep::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-list-bullet'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Workflow'; }
    public static function getNavigationSort(): ?int                    { return 2; }
    public static function getNavigationLabel(): string                 { return 'Schritte'; }
    public static function getModelLabel(): string                      { return 'Schritt'; }
    public static function getPluralModelLabel(): string                { return 'Schritte'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->schema([
                Select::make('phase_id')
                    ->label('Phase')
                    ->options(fn () => WorkflowPhase::orderBy('sort_order')->pluck('label', 'id'))
                    ->required()
                    ->searchable(),

                TextInput::make('label')
                    ->label('Bezeichnung')
                    ->required()
                    ->maxLength(120),

                TextInput::make('sort_order')
                    ->label('Reihenfolge')
                    ->numeric()
                    ->default(0)
                    ->helperText('Niedrigere Zahl = früher angezeigt'),
            ])->columns(2),
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

                TextColumn::make('phase.label')
                    ->label('Phase')
                    ->badge()
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Bezeichnung')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('updated_at')
                    ->label('Geändert')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query
                ->join('workflow_phases', 'workflow_steps.phase_id', '=', 'workflow_phases.id')
                ->orderBy('workflow_phases.sort_order')
                ->orderBy('workflow_steps.sort_order')
                ->select('workflow_steps.*')
            )
            ->reorderable('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkflowSteps::route('/'),
            'create' => Pages\CreateWorkflowStep::route('/create'),
            'edit'   => Pages\EditWorkflowStep::route('/{record}/edit'),
        ];
    }
}
