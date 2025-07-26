<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;
    public function mount($record): void
    {

        parent::mount($record);

        $Tenant = Tenant::with(['tenant_user', 'tenant_domain'])->findOrFail($record);

        $this->form->fill([
            'name' => $Tenant->tenant_user->name,
            'email' => $Tenant->tenant_user->email,
            'domain' => str_replace(app()->environment('production') ? '.m-dev.io' : '.localhost', '', $Tenant->tenant_domain->domain),
        ]);

    }

    protected function handleRecordUpdate($record, array $data): Tenant
    {

        $Tenant = Tenant::with(['tenant_user', 'tenant_domain'])->findOrFail($record->id);
        
        $user = User::where('tenant_id', $record->id)->first();

        if ($user) {
            $updateData = [];

            if (!empty($data['name']) && $Tenant->tenant_user->name !==$data['name']) {
                $updateData['name'] = $data['name'];
            }
            if (!empty($data['email']) && $Tenant->tenant_user->email !==$data['email']) {
                $updateData['email'] = $data['email'];
            }
            if (!empty($data['password'])) {
                $updateData['password'] = bcrypt($data['password']);
            }

            $user->update($updateData);
        }

        $Domain = str_replace(app()->environment('production') ? '.m-dev.io' : '.localhost', '', $Tenant->tenant_domain->domain);

        if (!empty($data['domain']) && $Domain !==$data['domain']) {
            $Domain = Domain::where('tenant_id', $Tenant->id)->first();

            $Domain->domain = Str::slug($data['domain']) . '.' . (app()->environment('production') ? 'm-dev.io' : 'localhost');

            $Domain->update();
        }

        return $record;

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
