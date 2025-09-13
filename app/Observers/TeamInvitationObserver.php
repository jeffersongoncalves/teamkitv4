<?php

namespace App\Observers;

use App\Models\TeamInvitation;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class TeamInvitationObserver
{
    /**
     * Handle the TeamInvitation "created" event.
     */
    public function created(TeamInvitation $teamInvitation): void
    {
        try {
            Cache::delete('team_invitations_count');
        } catch (InvalidArgumentException) {
        }
    }

    /**
     * Handle the TeamInvitation "updated" event.
     */
    public function updated(TeamInvitation $teamInvitation): void
    {
        //
    }

    /**
     * Handle the TeamInvitation "deleted" event.
     */
    public function deleted(TeamInvitation $teamInvitation): void
    {
        try {
            Cache::delete('team_invitations_count');
        } catch (InvalidArgumentException) {
        }
    }

    /**
     * Handle the TeamInvitation "restored" event.
     */
    public function restored(TeamInvitation $teamInvitation): void
    {
        //
    }

    /**
     * Handle the TeamInvitation "force deleted" event.
     */
    public function forceDeleted(TeamInvitation $teamInvitation): void
    {
        //
    }
}
