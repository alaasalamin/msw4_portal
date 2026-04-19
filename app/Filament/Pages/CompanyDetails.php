<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyDetails extends Page
{
    protected string $view = 'filament.pages.company-details';

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-building-office-2'; }
    public static function getNavigationGroup(): string|\UnitEnum|null  { return 'Configuration'; }
    public static function getNavigationLabel(): string                 { return 'Company Details'; }
    public function getTitle(): string                                  { return 'Company Details'; }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'owner_name'      => Setting::get('company_owner_name'),
            'company_name'    => Setting::get('company_name'),
            'company_email'   => Setting::get('company_email'),
            'company_phone'   => Setting::get('company_phone'),
            'street'          => Setting::get('company_street'),
            'house_number'    => Setting::get('company_house_number'),
            'postal_code'     => Setting::get('company_postal_code'),
            'city'            => Setting::get('company_city'),
            'country'         => Setting::get('company_country'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Owner')->schema([
                    TextInput::make('owner_name')
                        ->label('Owner Name')
                        ->required()
                        ->maxLength(100),
                ])->columns(1),

                Section::make('Company')->schema([
                    TextInput::make('company_name')
                        ->label('Company Name')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('company_email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(150),
                    TextInput::make('company_phone')
                        ->label('Phone Number')
                        ->tel()
                        ->maxLength(30),
                ])->columns(2),

                Section::make('Address')->schema([
                    TextInput::make('street')
                        ->label('Street')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('house_number')
                        ->label('House Number')
                        ->required()
                        ->maxLength(10),
                    TextInput::make('postal_code')
                        ->label('Postal Code')
                        ->required()
                        ->maxLength(20),
                    TextInput::make('city')
                        ->label('City')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('country')
                        ->label('Country')
                        ->required()
                        ->maxLength(100),
                ])->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('company_owner_name',  $data['owner_name']);
        Setting::set('company_name',        $data['company_name']);
        Setting::set('company_email',       $data['company_email']);
        Setting::set('company_phone',       $data['company_phone']);
        Setting::set('company_street',      $data['street']);
        Setting::set('company_house_number',$data['house_number']);
        Setting::set('company_postal_code', $data['postal_code']);
        Setting::set('company_city',        $data['city']);
        Setting::set('company_country',     $data['country']);

        Notification::make()
            ->title('Company details saved')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save')
                ->action('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }
}
