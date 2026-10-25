<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\HasUuid;
use App\Support\Navigation;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'avatar', 'password', 'is_admin', 'status', 'landing_page', 'uuid'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, HasUuid, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'is_admin',
        'status',
        'landing_page',
        'uuid',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'avatar_url',
    ];

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
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Check if the user is in an active status.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the URL to the user's profile photo/avatar.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return '/storage/'.ltrim($this->avatar, '/');
        }

        return null;
    }

    /**
     * Delete the user's avatar file from storage disk if it exists.
     */
    public function deleteAvatarFile(): void
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }
    }

    /**
     * Get the resolved URL for the user's default landing page upon login.
     * Checks user's direct landing page first, then falls back to their role's
     * default page, and finally to the Dashboard. Always verifies that the user
     * has permission to access the landing page before routing them there.
     */
    public function getLandingPageUrl(): string
    {
        if (! empty($this->landing_page) && Route::has($this->landing_page) && Navigation::userCanAccessRoute($this, $this->landing_page)) {
            return route($this->landing_page);
        }

        $roleLandingPages = $this->roles()
            ->whereNotNull('landing_page')
            ->where('landing_page', '!=', '')
            ->pluck('landing_page');

        foreach ($roleLandingPages as $page) {
            if (Route::has($page) && Navigation::userCanAccessRoute($this, $page)) {
                return route($page);
            }
        }

        return route('dashboard');
    }

    /**
     * Get the reports created by the user.
     *
     * @return HasMany<Report, $this>
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
