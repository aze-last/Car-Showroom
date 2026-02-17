<?php

namespace App\Policies;

use App\Models\UnitStatusLog;
use App\Models\User;

class UnitStatusLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, UnitStatusLog $unitStatusLog): bool
    {
        return $user->is_admin;
    }
}
