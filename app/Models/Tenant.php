<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, SoftDeletes;
    protected $fillable = [
        'id',
        'status',
        'slug'
    ];
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'status',
            'slug'
        ];
    }
    public function tenant_user()
    {
        return $this->belongsTo(User::class, 'id', 'tenant_id')->where('status', 1);
    }
    public function tenant_domain()
    {
        return $this->belongsTo(Domain::class, 'id', 'tenant_id');
    }
}