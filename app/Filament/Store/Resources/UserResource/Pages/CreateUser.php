<?php

namespace App\Filament\Store\Resources\UserResource\Pages;

use App\Filament\Store\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
