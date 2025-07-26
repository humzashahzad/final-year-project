<?php

namespace App\Filament\Resources\NavigationGroupResource\Pages;

use App\Filament\Resources\NavigationGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNavigationGroups extends ListRecords
{
    protected static string $resource = NavigationGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
