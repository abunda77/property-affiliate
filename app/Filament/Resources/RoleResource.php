<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as BaseRoleResource;
use Filament\Tables\Table;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Support\Facades\Log;

class RoleResource extends BaseRoleResource
{
    public static function table(Table $table): Table
    {
        Log::debug('RoleResource: Configuring table', [
            'table_class' => get_class($table),
        ]);

        try {
            // Get the parent table configuration first
            $parentTable = parent::table($table);
            
            Log::debug('RoleResource: Parent table configured', [
                'parent_table_class' => get_class($parentTable),
            ]);

            // Check if we have a valid table object before modifying filters
            if (method_exists($parentTable, 'filters')) {
                $result = $parentTable->filters([
                    // Empty filters to override parent
                ]);
            } else {
                // If parent doesn't have filters method, return as-is
                $result = $parentTable;
            }
            
            Log::debug('RoleResource: Table configured successfully');
            return $result;
        } catch (\Exception $e) {
            Log::error('RoleResource: Error configuring table', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Fallback: return a basic table configuration
            return $table
                ->columns([
                    // Basic columns that should always work
                ])
                ->filters([]);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
