<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;

class AlbumPolicy
{
    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Album $album): bool
    {
        return $album->user_id === $user->id || $album->status === 'published';
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, Album $album): bool
    {
        return $album->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, Album $album): bool
    {
        return $album->user_id === $user->id;
    }
}
