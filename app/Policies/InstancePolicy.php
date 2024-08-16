<?php

namespace App\Policies;

use App\Models\Instance;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Instance  $instance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Instance $instance)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Machine  $machine
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, Machine $machine)
    {
        /**
         * Se a instância for gratuita, o usuário pode criar
         * Se a instância for paga, o usuário só pode criar se tiver uma assinatura ativa
         */
        return $user->isAdmin() || ($user->activeSubscriptions->count() > 0 || $machine->isFree());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Instance  $instance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Instance $instance)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Instance  $instance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Instance $instance)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Instance  $instance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Instance $instance)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Instance  $instance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Instance $instance)
    {
        //
    }
}
