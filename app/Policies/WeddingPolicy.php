<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wedding;

class WeddingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Wedding $wedding): bool
    {
        return $user->id === $wedding->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Wedding $wedding): bool
    {
        return $user->id === $wedding->user_id;
    }

    public function delete(User $user, Wedding $wedding): bool
    {
        return $user->id === $wedding->user_id;
    }
}
