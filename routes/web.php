<?php

use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\Learning\LearningController;
use App\Http\Controllers\Learning\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::prefix('learning')->group(function () {
    Route::controller(LearningController::class)->group(function () {
        Route::get('/', 'index')->name('learning.index');    
        Route::get('/materi/{id}', 'show')->name('learning.show');
        Route::post('/materi/get-point', 'store')->name('learning.store');
    });
});

Route::prefix('kuis')->group(function () {
    Route::controller(QuizController::class)->group(function () {
        Route::get('{id}', 'show')->name('quiz.show');    
    });
});

Route::prefix('authenticate')->group(function () {
    Route::controller(AuthenticateController::class)->group(function () {
        Route::get('/login', 'index')->name('authenticate.login');
        Route::post('/login.post', 'post_login')->name('authenticate.login.post');
        Route::get('/register', 'create_view')->name('authenticate.register');
        Route::post('/register/post', 'store')->name('authenticate.register.post');
    });
});

