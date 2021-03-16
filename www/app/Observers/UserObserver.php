<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        Wallet::create([
            'funds' => 0.0,
            'user_id' => $user->id
        ]);
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $user->wallet->delete();
        $user->typeable->delete();
    }

    /**
     * Handle the User "retrieved" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function retrieved(User $user)
    {
        foreach ($user->typeable->toArray() as $key => $value) {
            $user->setAttribute($key, $value);
        }
    }
}
