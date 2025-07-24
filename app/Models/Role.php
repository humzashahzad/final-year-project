<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Role extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = ['status', 'name', 'description'];

}
