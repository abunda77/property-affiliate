<?php

namespace App\Filament\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\Roles\Pages\CreateRole as BaseCreateRole;
use App\Filament\Resources\RoleResource;

class CreateRole extends BaseCreateRole
{
    protected static string $resource = RoleResource::class;
}
