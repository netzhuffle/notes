<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('v1/notes', NoteController::class);
});
