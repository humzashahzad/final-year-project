<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;
    protected function handleRecordCreation(array $data): Tenant
    {
        
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $tenant = Tenant::create([
            'id' => Str::slug($data['domain']).'-'.''.Str::uuid(),
        ]);

        $tenant->domains()->create([
            'domain' => Str::slug($data['domain']).'.'.(app()->environment('production') ? 'm-dev.io' : 'localhost'),
            'name' => Str::slug($data['domain'])
        ]);
        
        User::where('id', $user->id)->update([
            'tenant_id' => $tenant->id
        ]);

        if ($tenant->id) {
            Storage::disk('store')->makeDirectory('tenant' . $tenant->id);
        }
        
        return $tenant;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
