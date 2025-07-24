<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use SoftDeletes;
    protected $fillable = ['status', 'category_id', 'name', 'description'];
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->where('status', 1);
    }
}