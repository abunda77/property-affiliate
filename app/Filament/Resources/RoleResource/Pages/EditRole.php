<?php

namespace App\Filament\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\Roles\Pages\EditRole as BaseEditRole;
use App\Filament\Resources\RoleResource;

class EditRole extends BaseEditRole
{
    protected static string $resource = RoleResource::class;
}
