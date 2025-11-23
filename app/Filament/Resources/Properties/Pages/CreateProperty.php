<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Transform features array structure
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_map(function ($item) {
                return is_array($item) && isset($item['feature']) ? $item['feature'] : $item;
            }, $data['features']);
        }

        return $data;
    }
}
