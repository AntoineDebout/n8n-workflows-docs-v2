<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/workflows', [WorkflowController::class, 'index'])
         ->name('workflows.index');
    
    Route::get('/workflows/create', [WorkflowController::class, 'create'])
         ->name('workflows.create');
    
    Route::post('/workflows', [WorkflowController::class, 'store'])
         ->name('workflows.store');
    
    Route::get('/workflows/{slug}', [WorkflowController::class, 'show'])
         ->name('workflows.show');
    
    Route::get('/workflows/{slug}/edit', [WorkflowController::class, 'edit'])
         ->name('workflows.edit');
    
    Route::put('/workflows/{slug}', [WorkflowController::class, 'update'])
         ->name('workflows.update');
    
    Route::delete('/workflows/{slug}', [WorkflowController::class, 'destroy'])
         ->name('workflows.destroy');
});

Route::get('/workflows/{slug}/public', [WorkflowController::class, 'showPublic'])
    ->name('workflows.public.show');

require __DIR__.'/auth.php';
