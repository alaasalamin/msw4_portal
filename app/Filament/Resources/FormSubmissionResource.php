<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormSubmissionResource\Pages;
use App\Models\CustomForm;
use App\Models\FormSubmission;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-inbox-arrow-down'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Content'; }
    public static function getNavigationSort(): ?int                    { return 4; }
    public static function getNavigationLabel(): string                 { return 'Form Submissions'; }
    public static function getModelLabel(): string                      { return 'Submission'; }
    public static function getPluralModelLabel(): string                { return 'Form Submissions'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('form.name')
                    ->label('Form')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('page_slug')
                    ->label('Page')
                    ->placeholder('—')
                    ->color('gray'),
                TextColumn::make('data')
                    ->label('Preview')
                    ->formatStateUsing(function ($state) {
                        if (! is_array($state)) return '—';
                        return collect($state)
                            ->map(fn ($v, $k) => "$k: $v")
                            ->take(2)
                            ->implode(' · ');
                    })
                    ->wrap(),
                TextColumn::make('ip_address')->label('IP')->color('gray'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('form_id')
                    ->label('Form')
                    ->options(fn () => CustomForm::pluck('name', 'id')),
                TrashedFilter::make(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (FormSubmission $record) => 'Submission — ' . $record->form?->name)
                    ->modalContent(fn (FormSubmission $record) => view('filament.modals.submission-detail', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (FormSubmission $record) => ! $record->trashed()),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Move to trash?')
                    ->modalDescription('This submission will be moved to trash. You can restore it later.')
                    ->modalSubmitActionLabel('Move to trash')
                    ->visible(fn (FormSubmission $record) => ! $record->trashed()),
                RestoreAction::make()
                    ->requiresConfirmation()
                    ->visible(fn (FormSubmission $record) => $record->trashed()),
                ForceDeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Permanently delete?')
                    ->modalDescription('This submission will be permanently deleted and cannot be recovered.')
                    ->modalSubmitActionLabel('Delete permanently')
                    ->visible(fn (FormSubmission $record) => $record->trashed()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Move selected to trash?')
                        ->modalDescription('Selected submissions will be moved to trash. You can restore them later.')
                        ->modalSubmitActionLabel('Move to trash'),
                    RestoreBulkAction::make()->requiresConfirmation(),
                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Permanently delete selected?')
                        ->modalDescription('These submissions will be permanently deleted and cannot be recovered.')
                        ->modalSubmitActionLabel('Delete permanently'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormSubmissions::route('/'),
        ];
    }
}
