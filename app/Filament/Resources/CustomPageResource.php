<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomPageResource\Pages;
use App\Models\CustomPage;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomPageResource extends Resource
{
    protected static ?string $model = CustomPage::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-rectangle-stack'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Settings'; }
    public static function getNavigationSort(): ?int                    { return 10; }
    public static function getNavigationLabel(): string                 { return 'Dynamic Pages'; }
    public static function getModelLabel(): string                      { return 'Dynamic Page'; }

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->schema([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('slug', \Illuminate\Support\Str::slug($state))
                    ),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->prefix('/admin/board/')
                    ->helperText('Auto-generated from name. Change if needed.'),
                Select::make('icon')
                    ->options([
                        'heroicon-o-clipboard-document-list' => 'Clipboard List',
                        'heroicon-o-wrench-screwdriver'      => 'Wrench',
                        'heroicon-o-shopping-cart'           => 'Shopping Cart',
                        'heroicon-o-inbox'                   => 'Inbox',
                        'heroicon-o-clock'                   => 'Clock',
                        'heroicon-o-exclamation-triangle'    => 'Warning',
                        'heroicon-o-check-circle'            => 'Check Circle',
                        'heroicon-o-archive-box'             => 'Archive Box',
                        'heroicon-o-queue-list'              => 'Queue List',
                        'heroicon-o-squares-2x2'             => 'Grid',
                    ])
                    ->default('heroicon-o-clipboard-document-list')
                    ->required(),
                ColorPicker::make('color')->default('#6366f1'),
                TextInput::make('sort_order')->numeric()->default(0),
                Textarea::make('description')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->color('gray'),
                TextColumn::make('icon')->color('gray'),
                TextColumn::make('color')
                    ->formatStateUsing(fn ($state) => $state)
                    ->badge(),
                TextColumn::make('activeEntries_count')
                    ->label('Open entries')
                    ->counts('activeEntries')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomPages::route('/'),
            'create' => Pages\CreateCustomPage::route('/create'),
            'edit'   => Pages\EditCustomPage::route('/{record}/edit'),
        ];
    }
}
