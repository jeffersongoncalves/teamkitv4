<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class TeamPolicy
{
    /**
     * Determine whether the authenticatable can view any models.
     */
    public function viewAny(Authenticatable $authenticatable): bool
    {
        return true;
    }

    /**
     * Determine whether the authenticatable can view the model.
     */
    public function view(Authenticatable $authenticatable, Team $team): bool
    {
        return true;
    }

    /**
     * Determine whether the authenticatable can create models.
     */
    public function create(Authenticatable $authenticatable): bool
    {
        return true;
    }

    /**
     * Determine whether the authenticatable can update the model.
     */
    public function update(Authenticatable $authenticatable, Team $team): bool
    {
        if ($authenticatable instanceof User) {
            return $team->user_id === $authenticatable->id;
        }

        return true;
    }

    /**
     * Determine whether the authenticatable can delete the model.
     */
    public function delete(Authenticatable $authenticatable, Team $team): bool
    {
        return $team->personal_team === false;
    }

    /**
     * Determine whether the authenticatable can restore the model.
     */
    public function restore(Authenticatable $authenticatable, Team $team): bool
    {
        return false;
    }

    /**
     * Determine whether the authenticatable can permanently delete the model.
     */
    public function forceDelete(Authenticatable $authenticatable, Team $team): bool
    {
        return false;
    }
}
