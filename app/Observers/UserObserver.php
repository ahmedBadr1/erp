<?php

namespace App\Observers;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Notifications\WelcomeMessage;
use Spatie\Permission\Models\Role;

class UserObserver
{
    public $afterCommit = true;
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $user->notify(new WelcomeMessage());

//        if (!$user->HasAnyRole(Role::all())){
//            $role = Role::findByName('admin','api');
//            $user->assignRole($role);
//        }
        if(!$user->profile()->exists()){
            $user->profile()->create([
                'bio' => 'Employee'
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // $user->notify(new UserUpdated());
        if (!$user->HasAnyRole(Role::all())){
            $role = Role::findByName('admin');
            $user->assignRole($role);
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        // $user->notify(new UserDeleted());
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        // $user->notify(new UserRestored());
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
