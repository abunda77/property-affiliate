<?php

namespace App\Filament\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole as BaseViewRole;
use App\Filament\Resources\RoleResource;

class ViewRole extends BaseViewRole
{
    protected static string $resource = RoleResource::class;
}
