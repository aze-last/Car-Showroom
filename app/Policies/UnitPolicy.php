<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, Unit $unit): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->is_admin;
    }

    public function restore(User $user, Unit $unit): bool
    {
        return $user->is_admin;
    }

    public function forceDelete(User $user, Unit $unit): bool
    {
        return false;
    }

    public function changeStatus(User $user, Unit $unit): bool
    {
        return $user->isStaff();
    }

    public function viewQr(User $user, Unit $unit): bool
    {
        return $user->isStaff();
    }

    public function manageImages(User $user, Unit $unit): bool
    {
        return $user->is_admin;
    }
}
