<?php

namespace App\Observers;

use App\Models\Team;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class TeamObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(Team $team): void
    {
        try {
            Cache::delete('teams_count');
        } catch (InvalidArgumentException) {
        }
    }

    /**
     * Handle the Team "updated" event.
     */
    public function updated(Team $team): void
    {
        //
    }

    /**
     * Handle the Team "deleted" event.
     */
    public function deleted(Team $team): void
    {
        try {
            Cache::delete('teams_count');
        } catch (InvalidArgumentException) {
        }
    }

    /**
     * Handle the Team "restored" event.
     */
    public function restored(Team $team): void
    {
        //
    }

    /**
     * Handle the Team "force deleted" event.
     */
    public function forceDeleted(Team $team): void
    {
        //
    }
}
