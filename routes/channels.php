<?php

use Illuminate\Support\Facades\Broadcast;

// Register auth route accepting both web and admin guards
Broadcast::routes(['middleware' => ['auth:web,admin']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
}, ['guards' => ['web', 'admin', 'employee']]);
