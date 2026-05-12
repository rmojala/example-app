<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth'])
    ->group(function () {
        Route::resource('notes', NoteController::class);
    });

Route::prefix('admin')
    ->middleware(['admin'])
    ->group(function () {
        Route::get('users', [UserController::class, 'index'])
            ->name('admin');

        Route::patch('users/bulk-update', [UserController::class, 'bulkUpdate']);
    });

require __DIR__.'/settings.php';
