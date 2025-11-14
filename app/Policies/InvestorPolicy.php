<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Investor;

class InvestorPolicy
{
    /**
     * Determine if the user can update the investor profile.
     */
    public function update(User $user, Investor $investor): bool
    {
        // User can only update their own profile
        return $user->id === $investor->user_id;
    }
}