<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function onlyUser(User $user)

    {
        if($user->type == 'u'){
            return true;
        }else{
            return false;
        }
    }

    public function onlyOperator(User $user)

    {
        if($user->type == 'o'){
            return true;
        }else{
            return false;
        }
    }

    public function onlyAdmin(User $user)

    {
        if($user->type == 'a'){
            return true;
        }else{
            return false;
        }
    }
}
