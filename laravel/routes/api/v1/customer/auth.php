<?php

use App\Http\Controllers\V1\Customer\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Authentication Routes
|--------------------------------------------------------------------------
|
| Here is where you can register customer authentication routes for your application.
|
*/

Route::group([
    'prefix' => 'customer/auth',
    'controller' => AuthController::class,
], function () {
    Route::post('/login', 'login')->name('customer.login');
    Route::post('/register', 'register')->name('customer.register');
    Route::post('/forgot', 'forgot')->name('customer.forgot');
    Route::post('/logout', 'logout')->middleware('auth:sanctum')->name('customer.logout');
});
