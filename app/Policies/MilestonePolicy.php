<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MilestonePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Milestone $milestone
     * @return Response|bool
     */
    public function view(User $user, Milestone $milestone)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Milestone $milestone
     * @return Response|bool
     */
    public function update(User $user, Milestone $milestone)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Milestone $milestone
     * @return Response|bool
     */
    public function delete(User $user, Milestone $milestone)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Milestone $milestone
     * @return Response|bool
     */
    public function restore(User $user, Milestone $milestone)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Milestone $milestone
     * @return Response|bool
     */
    public function forceDelete(User $user, Milestone $milestone)
    {
        //
    }
}
