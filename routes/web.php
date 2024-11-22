<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\RegisterController;

Route::post('login', [LoginAuthController::class, 'login']);
Route::post('register', [RegisterController::class, 'store']);
Route::post('logout', [LoginAuthController::class, 'logout']);

// For authenticated user
Route::group(['middleware' => ['auth']], function () {
    Route::get('home', function () {
        $page = 'home';
        $dataType = 0;
        return view('home', compact('page', 'dataType'));
    });

    Route::get('completed-tasks', function () {
        $page = 'completed';
        $dataType = 2;
        return view('home', compact('page','dataType'));
    });

    Route::get('todo-tasks', function () {
        $page = 'todo';
        $dataType = 3;
        return view('home', compact('page', 'dataType'));
    });

    Route::get('archived-tasks', function () {
        $page = 'archived';
        $dataType = 1;
        return view('home', compact('page', 'dataType'));
    });
});

// Access for guest only
Route::group(['middleware' => ['guest']], function () {
    //
    Route::get('/', function () {
        return view('login');
    })->name('login');
    
    Route::get('register', function () {
        return view('register');
    });
});