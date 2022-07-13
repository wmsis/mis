<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('test-channel', function($user){
    return true;
});

//通配符参数作为其后续参数，{userId}占位符
Broadcast::channel('user.{userId}', function($user, $userId){
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('alarm-notify.{userId}', function($user, $userId){
    return (int) $user->id === (int) $userId;
});
