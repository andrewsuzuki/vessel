<?php

/*
|--------------------------------------------------------------------------
| Event listeners
|--------------------------------------------------------------------------
*/

Event::listen('auth.login', function($user)
{
    $user->last_login = Carbon\Carbon::now();
    $user->timestamps = false;
    $user->save();
});