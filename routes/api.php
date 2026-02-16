<?php

declare(strict_types=1);

use App\Http\Controllers\Api\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('password')->name('password.')->group(function () {
    Route::post('/generate', [PasswordController::class, 'generate'])
        ->name('generate');

    Route::post('/hash', [PasswordController::class, 'hash'])
        ->name('hash');

    Route::post('/verify', [PasswordController::class, 'verify'])
        ->name('verify');
});
