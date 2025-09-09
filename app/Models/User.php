<?php

namespace App\Models;

use App\Observers\UserObserver;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property bool $status
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $avatar_url
 * @property array<array-key, mixed>|null $custom_fields
 * @property string|null $locale
 * @property string|null $theme_color
 * @property int|null $current_team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team|null $currentTeam
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read \App\Models\Membership|null $membership
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 *
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCustomFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereThemeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(UserObserver::class)]
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, FilamentUser, HasAvatar, HasDefaultTenant, HasTenants, MustVerifyEmailContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use HasFactory;
    use MustVerifyEmail;
    use Notifiable;

    protected $fillable = [
        'status',
        'name',
        'email',
        'password',
        'avatar_url',
        'custom_fields',
        'locale',
        'theme_color',
        'current_team_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @throws \Exception
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return false;
        }

        return true;
    }

    public function canImpersonate(): bool
    {
        return false;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');

        return $this->$avatarColumn ? Storage::url($this->$avatarColumn) : null;
    }

    public function currentTeam(): BelongsTo
    {
        if (is_null($this->current_team_id) && $this->id) {
            $this->switchTeam($this->personalTeam());
        }

        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function switchTeam($team): bool
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        $this->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        $this->setRelation('currentTeam', $team);

        return true;
    }

    public function belongsToTeam($team): bool
    {
        return $this->ownsTeam($team) || $this->teams->contains(fn ($t) => $t->id === $team->id);
    }

    public function ownsTeam($team): bool
    {
        if (is_null($team)) {
            return false;
        }

        return $this->id == $team->user_id;
    }

    public function personalTeam(): ?Team
    {
        return $this->ownedTeams->where('personal_team', true)->first();
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->belongsToTeam($tenant);
    }

    public function getTenants(Panel $panel): array|Collection
    {
        return $this->ownedTeams->merge($this->teams)->sortBy('name');
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, Membership::class)
            ->withTimestamps()
            ->as('membership');
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->currentTeam;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
            'custom_fields' => 'array',
        ];
    }
}
