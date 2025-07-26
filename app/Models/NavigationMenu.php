<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class NavigationMenu extends Model
{
    use SoftDeletes, BelongsToTenant;
    protected $fillable = [ 'status', 'group_id', 'name', 'order', 'slug', 'page_path', 'encryption_salt', 'icon', 'color', 'objects' ];
    protected $casts = [
        'objects' => 'array',
    ];
    protected $guarded = ['tenant_id'];
    /**
     * Encrypt the path using the given salt.
     */
    public static function encryptPath(string $path, string $salt): string
    {
        return Crypt::encryptString("{$path}|{$salt}");
    }

    /**
     * Check if the given value is already encrypted.
     */
    public static function isEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Model event hooks for encryption logic.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->encryption_salt) {
                $model->encryption_salt = (string) Str::uuid();
            }

            // Only encrypt if not already encrypted and value exists
            if ($model->page_path && !static::isEncrypted($model->page_path)) {
                $model->page_path = static::encryptPath($model->page_path, $model->encryption_salt);
            }
        });

        static::updating(function ($model) {
            // Get the original value before any changes
            $originalPath = $model->getOriginal('page_path');
            
            // Only update encryption if the path was actually changed
            if ($model->isDirty('page_path') && $model->page_path !== $originalPath) {
                if (!$model->encryption_salt) {
                    $model->encryption_salt = (string) Str::uuid();
                }
                
                if ($model->page_path && !static::isEncrypted($model->page_path)) {
                    $model->page_path = static::encryptPath($model->page_path, $model->encryption_salt);
                }
            }
        });
    }
    /**
     * Accessor for page_path - returns decrypted value or status message.
     */
    public function getPagePathAttribute($value)
    {
        if (!$value || !$this->encryption_salt) {
            return null;
        }
    
        try {
            $decrypted = Crypt::decryptString($value);
            [$path, $salt] = explode('|', $decrypted, 2);
            return $salt === $this->encryption_salt ? $path : 'Tampered';
        } catch (\Exception $e) {
            return 'Invalid';
        }
    }
    
    /**
     * Mutator for page_path - handles encryption when setting value.
     */
    public function setPagePathAttribute($value)
    {
        // If the value is already encrypted, store it directly
        if (self::isEncrypted($value)) {
            $this->attributes['page_path'] = $value;
            return;
        }
        
        // If we don't have a salt, generate one
        if (!$this->encryption_salt) {
            $this->encryption_salt = (string) Str::uuid();
        }
        
        // Encrypt the new value if it's not empty
        $this->attributes['page_path'] = $value ? self::encryptPath($value, $this->encryption_salt) : null;
    }
    public function group()
    {
        return $this->belongsTo(NavigationGroup::class)->where('status', 1);
    }
    public function tenant()
    {
        return $this->hasOne(Tenant::class)->where('status', 1);
    }
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'tenant_id', 'tenant_id');
    }

}
