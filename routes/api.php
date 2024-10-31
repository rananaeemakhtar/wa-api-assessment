<?php

use App\Http\Controllers\Api\AuthContoller;
use App\Http\Controllers\Api\ChatroomController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * @OA\Info(
 *     title="Chat Application API",
 *     version="1.0.0",
 *     description="API for managing chatrooms and messages.",
 *     @OA\Contact(
 *         name="Support",
 *         email="support@example.com"
 *     )
 * )
 */


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register', [AuthContoller::class, 'login']);
Route::post('login', [AuthContoller::class, 'register']);
Route::get('login', [AuthContoller::class, 'logout']);

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('chatrooms', [ChatroomController::class, 'index']);
    Route::post('chatrooms', [ChatroomController::class, 'store']);
    Route::post('chatrooms/{chatroom}/enter', [ChatroomController::class, 'enter'])->name('chatrooms.enter');
    Route::post('chatrooms/{chatroom}/leave', [ChatroomController::class, 'leave'])->name('chatrooms.leave');
    
    Route::get('chatrooms/{chatroom}/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('chatrooms/{chatroom}/messages', [MessageController::class, 'store'])->name('messages.store');
});
