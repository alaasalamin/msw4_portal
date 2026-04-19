<?php

namespace App\Filament\Resources\CustomFormResource\Pages;

use App\Filament\Resources\CustomFormResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditCustomForm extends EditRecord
{
    protected static string $resource = CustomFormResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    public function addSpacer(): void
    {
        $fields = $this->data['fields'] ?? [];
        $fields[Str::uuid()->toString()] = ['label' => 'Spacer', 'type' => 'spacer', 'is_required' => false, 'col_span' => 'full', 'options' => []];
        $this->data['fields'] = $fields;
    }

    public function addSubHeading(): void
    {
        $fields = $this->data['fields'] ?? [];
        $fields[Str::uuid()->toString()] = ['label' => 'Section Title', 'type' => 'subheading', 'placeholder' => 'Optional subtitle', 'is_required' => false, 'col_span' => 'full', 'options' => []];
        $this->data['fields'] = $fields;
    }

    public function addCustomerPackage(): void
    {
        $fields = $this->data['fields'] ?? [];

        foreach ($this->customerPackageFields() as $field) {
            $fields[Str::uuid()->toString()] = $field;
        }

        $this->data['fields'] = $fields;
    }

    private function customerPackageFields(): array
    {
        return [
            ['label' => 'Name',        'type' => 'text',  'is_required' => true,  'placeholder' => 'Full name',         'col_span' => 'half', 'options' => []],
            ['label' => 'Email',       'type' => 'email', 'is_required' => true,  'placeholder' => 'email@example.com', 'col_span' => 'half', 'options' => []],
            ['label' => 'Phone',       'type' => 'tel',   'is_required' => false, 'placeholder' => '+49 123 456789',    'col_span' => 'full', 'options' => []],
            ['label' => 'Street',      'type' => 'text',  'is_required' => false, 'placeholder' => 'Street name',       'col_span' => 'half', 'options' => []],
            ['label' => 'House No.',   'type' => 'text',  'is_required' => false, 'placeholder' => '14',                'col_span' => 'half', 'options' => []],
            ['label' => 'Postal Code', 'type' => 'text',  'is_required' => false, 'placeholder' => '12345',             'col_span' => 'half', 'options' => []],
            ['label' => 'City',        'type' => 'text',  'is_required' => false, 'placeholder' => 'City',              'col_span' => 'half', 'options' => []],
        ];
    }
}
