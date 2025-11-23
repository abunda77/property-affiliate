<?php

namespace App\Filament\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ListRoles as BaseListRoles;
use App\Filament\Resources\RoleResource;

class ListRoles extends BaseListRoles
{
    protected static string $resource = RoleResource::class;
}
