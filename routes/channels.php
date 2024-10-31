<?php

use App\Models\Chatroom;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chatroom.{chatroomId}', function ($user, $chatroomId) {
    $chatroom = Chatroom::find($chatroomId);

    return $chatroom && $chatroom->users->contains($user->id);
});
