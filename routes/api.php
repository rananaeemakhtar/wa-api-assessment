<?php

use App\Http\Controllers\Api\ChatroomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('chatrooms', [ChatroomController::class, 'index']);
    Route::post('chatrooms', [ChatroomController::class, 'store']);
    Route::post('chatrooms/{chatroom}/enter', [ChatroomController::class, 'enter'])->name('chatrooms.enter');
    Route::post('chatrooms/{chatroom}/leave', [ChatroomController::class, 'leave'])->name('chatrooms.leave');
});
