<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Mail\InviteUserToAssessment;
use Filament\Notifications\Notification;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends \Stats4sd\FilamentTeamManagement\Models\User
{
    use HasApiTokens, HasFactory;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        return true;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->isAdmin() || $this->assessments->contains($tenant->id);
    }

    public function getTenants(Panel $panel): Collection
    {
        if($this->isAdmin()) {
            return Assessment::all();
        }

        return $this->assessments;
    }

    /** @return BelongsToMany<Assessment, $this> */
    public function assessments(): BelongsToMany
    {
        return $this->teams();
    }
}
