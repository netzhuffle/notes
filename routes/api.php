<?php

use App\Http\Controllers\NoteController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('v1/notes', NoteController::class);
});

// This route is to enable easy testing bypassing user registration and login.
Route::post('v1/users/{user}/token', function (User $user) {
    $token = $user->createToken('test-token');

    return ['token' => $token->plainTextToken];
});
