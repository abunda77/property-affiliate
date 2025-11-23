<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Transform features array for repeater
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_map(function ($feature) {
                return is_array($feature) ? $feature : ['feature' => $feature];
            }, $data['features']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Transform features array structure back
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_map(function ($item) {
                return is_array($item) && isset($item['feature']) ? $item['feature'] : $item;
            }, $data['features']);
        }

        return $data;
    }
}
