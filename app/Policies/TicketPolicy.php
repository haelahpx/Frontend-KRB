<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket)
    {
        // ---------------------------------------------------------
        // 1. CHECK YOUR 'is_agent' COLUMN (ENUM: 'yes' or 'no')
        // ---------------------------------------------------------
        if ($user->is_agent === 'yes') {
            return true;
        }

        // ---------------------------------------------------------
        // 2. OPTIONAL: CHECK role_id FOR SUPERADMINS
        // ---------------------------------------------------------
        // If your Superadmin has 'is_agent' set to 'no', but has role_id = 1,
        // uncomment the lines below:
        /*
        if ($user->role_id === 1) {
            return true;
        }
        */

        // ---------------------------------------------------------
        // 3. ALLOW OWNER (The Customer who created it)
        // ---------------------------------------------------------
        if ($user->id === $ticket->user_id) {
            return true;
        }

        // ---------------------------------------------------------
        // 4. ALLOW ASSIGNED AGENT (Specific assignment)
        // ---------------------------------------------------------
        if ($user->id === $ticket->assigned_to_user_id) {
            return true;
        }

        return false;
    }

    // Ensure these are allowed for agents too if needed
    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Ticket $ticket)
    {
        if ($user->is_agent === 'yes') {
            return true;
        }
        return $user->id === $ticket->user_id;
    }
}