<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, BelongsToTenant, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'status',
        'tenant_id',
        'avatar_url',
        'name',
        'email',
        'password',
    ];
    protected $guarded = ['tenant_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function tenant()
    {
        return $this->hasOne(Tenant::class)->where('status', 1);
    }
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'tenant_id', 'tenant_id');
    }
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return empty($this->tenant_id);
        }

        if ($panel->getId() === 'store') {
            return !empty($this->tenant_id);
        }

        return false;
    }
    // public function getFilamentAvatarUrl(): ?string
    // {
    //     /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        
    //     $disk = Storage::disk(empty($this->tenant_id)? 'admin-profile-photos' : 'tenants/' . 'tenant' . tenant('id') . '/store-profile-photos' );

    //     return $this->avatar_url ? $disk->url($this->avatar_url) : null;
    // }
    public function getFilamentAvatarUrl(): ?string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        if (empty($this->tenant_id)) {

            $disk = Storage::disk('admin');

            return $this->avatar_url ? $disk->url($this->avatar_url) : null;

        }

        // Tenant user avatar
        return $this->avatar_url
            ? Storage::disk('store')->url($this->avatar_url)
            : null;
    }
}
