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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//通配符参数作为其后续参数，{userId}占位符
Broadcast::channel('user.{userId}', function($user, $userId){
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('task.{userId}', function($user, $userId){
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('test-channel', function($user){
    return true;
});
