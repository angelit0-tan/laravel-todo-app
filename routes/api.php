<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MarkTaskController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\UploadController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');


Route::post('login', [LoginAuthController::class, 'login']);
Route::post('register', [RegisterController::class, 'store']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('upload', UploadController::class);
    Route::post('tags', [TagController::class, 'store']);

    // Validate user, if owner of the tasks
    Route::group(['middleware' => ['marked-ownership-validation']], function () {
        Route::patch('tasks/{task}/complete', [MarkTaskController::class, 'markedAsComplete']);
        Route::patch('tasks/{task}/incomplete', [MarkTaskController::class, 'markedAsIncomplete']);
        Route::patch('tasks/{task}/archive', [MarkTaskController::class, 'markedAsArchived']);
        Route::patch('tasks/{task}/restore', [MarkTaskController::class, 'markedRestoreArchived']);
    });
});