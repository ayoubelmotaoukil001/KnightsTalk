<?php

use App\Http\Controllers\Admin\PuzzleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('puzzles', [PuzzleController::class, 'index'])->name('puzzles.index');
        Route::get('puzzles/create', [PuzzleController::class, 'create'])->name('puzzles.create');
        Route::post('puzzles', [PuzzleController::class, 'store'])->name('puzzles.store');
        Route::get('puzzles/{puzzle}/play', [PuzzleController::class, 'play'])->name('puzzles.play');
        Route::get('puzzles/{puzzle}/edit', [PuzzleController::class, 'edit'])->name('puzzles.edit');
        Route::put('puzzles/{puzzle}', [PuzzleController::class, 'update'])->name('puzzles.update');
        Route::delete('puzzles/{puzzle}', [PuzzleController::class, 'destroy'])->name('puzzles.destroy');
    });
});

require __DIR__.'/auth.php';
