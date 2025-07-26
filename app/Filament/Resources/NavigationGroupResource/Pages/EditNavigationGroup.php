<?php

namespace App\Filament\Resources\NavigationGroupResource\Pages;

use App\Filament\Resources\NavigationGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNavigationGroup extends EditRecord
{
    protected static string $resource = NavigationGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
