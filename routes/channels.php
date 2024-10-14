<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

\Illuminate\Support\Facades\Route::get('/test-ws-route', function () {
    event(new \App\Events\TestEvent());
});
