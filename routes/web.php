<?php

use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\JurnalPenyesuaian;
use App\Http\Controllers\JurnalPenyesuaianController;
use App\Http\Controllers\Learning\LearningController;
use App\Http\Controllers\Learning\QuizController;
use App\Http\Controllers\NeracaSaldoController;
use App\Http\Controllers\NeracaSaldoSetelahPenyesuaianController;
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
        Route::post('{id}/submit', 'submitScore')->name('quiz.submit');
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
Route::prefix('dokumen')->group(function () {
    Route::controller(BukuBesarController::class)->group(function () {
        Route::get('/buku-besar', 'index')->name('buku_besar.index');
    });

    Route::controller(NeracaSaldoController::class)->group(function () {
        Route::get('/neraca-saldo', 'index')->name('neraca_saldo.index');
    });
    
    Route::controller(JurnalPenyesuaianController::class)->group(function () {
        Route::get('/jurnal-penyesuaian', 'index')->name('jurnal_penyesuaian.index');
    });

    Route::controller(NeracaSaldoSetelahPenyesuaianController::class)->group(function () {
        Route::get('/neraca-saldo-setelah-penyesuaian', 'index')->name('neraca_saldo_setelah_penyesuaian.index');
    });
});

