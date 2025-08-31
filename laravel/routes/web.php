<?php

use App\Livewire\ResetPasswordForm;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});

Route::redirect('/', '/admin');

Route::get('/customer/auth/password/reset/{token}', ResetPasswordForm::class)->name('password.reset');
