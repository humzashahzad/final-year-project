<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class RolePermission extends Model
{
    
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['status', 'role_id', 'permissions'];
    protected $guarded = ['tenant_id'];
    protected $casts = [
        'permissions' => 'array',
    ];
    public function tenant()
    {
        return $this->hasOne(Tenant::class)->where('status', 1);
    }
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'tenant_id', 'tenant_id');
    }
}
