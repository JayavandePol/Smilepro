<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function assignRole(User $authUser, User $targetUser): bool
    {
        // Only praktijkmanagement can assign roles
        return $authUser->hasRole('praktijkmanagement');
    }
}
