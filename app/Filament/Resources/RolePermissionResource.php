<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolePermissionResource\Pages;
use App\Filament\Resources\RolePermissionResource\RelationManagers;
use App\Models\NavigationMenu;
use App\Models\Role;
use App\Models\RolePermission;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class RolePermissionResource extends Resource
{
    protected static ?string $model = RolePermission::class;


    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-rectangle-stack';
    }
    public static function canCreate(): bool
    {  
        return true;
    }
    public static function canViewAny(): bool
    {
        return true;
    }
    public static function canEdit($record): bool
    {
        return true;
    }
    public static function canDelete($record): bool
    {   
        return true;
    }
    public static function shouldRegisterNavigation(): bool
    {   
        return true;
    }
    public static function getNavigationSort(): int
    {
        return 1;
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Role & Permissions';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Create New Post') // Custom modal title
                    ->modalWidth('7xl') // Extra large modal
                    ->modalSubmitActionLabel('Save Post') // Custom submit button text
                    ->form([]) // Explicitly use your form definition
                    ->using(function (array $data) {
                        // Custom create logic
                        return static::getModel()::create($data);
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRolePermissions::route('/'),
            // 'create' => Pages\CreateRolePermission::route('/create'),
            // 'edit' => Pages\EditRolePermission::route('/{record}/edit'),
        ];
    }
}
