<?php

namespace App\Filament\Resources\RolePermissionResource\Pages;

use App\Filament\Resources\RolePermissionResource;
use App\Models\RolePermission;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\NavigationGroup;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Form;
use App\Models\NavigationMenu;
use Illuminate\Support\Str;
use App\Filament\Resources\RolePermissionResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class CreateRolePermission extends CreateRecord
{
    protected static string $resource = RolePermissionResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('role_id')
                    ->label('Roles')
                    ->options(Role::where('status', 1)->pluck('name', 'id')->toArray())
                    ->required()
                    ->multiple()
                    ->columnSpan(2)
                    ->searchable(),
                Tabs::make('permissions')
                    ->columnSpanFull()
                    ->tabs(
                        NavigationMenu::where('status', 1)->get()->map(function ($menu) {
                            return Tab::make($menu->name)
                                ->icon($menu->icon)
                                ->badge(badge: COUNT($menu->objects))
                                ->schema(function (Get $get) use ($menu) {
                                    return array_map(function ($object) use ($menu) {
                                        $fieldName = "{$menu->id}|" . Str::slug($menu->name) . "|{$object['object']}";
                                        
                                        return Checkbox::make($fieldName)
                                            ->label(Str::headline($object['object']))
                                            ->dehydrated(true)
                                            ->columnSpan(1)
                                            ->afterStateHydrated(fn (Checkbox $component, $state) => 
                                                $component->state((bool) $state)
                                            );
                                    }, $menu->objects);
                                })
                                ->columns(5);
                        })->toArray()
                    ),
            ]);
    }
    protected function handleRecordCreation(array $data): RolePermission
    {
        // Extract role IDs (they might be an array if multiple select is enabled)
        $roleIds = Arr::wrap($data['role_id'] ?? []);
        unset($data['role_id']);

        dd($roleIds);

        // Process permissions data
        $permissions = [];
        foreach ($data as $key => $value) {
            if ($value === true) { // Only store checked permissions
                $parts = explode('|', $key);
                if (count($parts) === 3) {
                    $permissions[] = [
                        'menu_id' => $parts[0],
                        'menu_name' => $parts[1],
                        'object' => $parts[2],
                    ];
                }
            }
        }

        // Create records for each role
        foreach ($roleIds as $roleId) {
            RolePermission::create([
                'role_id' => $roleId,
                'permissions' => $permissions,
            ]);
        }

        // Return the last created record (or modify as needed)
        return RolePermission::latest()->first();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}