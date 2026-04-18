<?php

use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\PuzzleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProGameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PuzzleController as UserPuzzleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo');

    Route::get('/puzzles', [UserPuzzleController::class, 'index'])->name('puzzles.index');
    Route::get('/puzzles/{puzzle}/play', [UserPuzzleController::class, 'play'])->name('puzzles.play');
    Route::post('/puzzles/{puzzle}/complete', [UserPuzzleController::class, 'complete'])->name('puzzles.complete');
    Route::post('/puzzles/{puzzle}/attempt', [UserPuzzleController::class, 'attempt'])->name('puzzles.attempt');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/chat/{message}', [ChatController::class, 'destroy'])->name('chat.destroy');

    Route::get('/pro-games', [ProGameController::class, 'index'])->name('pro-games.index');
    Route::get('/pro-games/create', [ProGameController::class, 'create'])->name('pro-games.create');
    Route::post('/pro-games', [ProGameController::class, 'store'])->name('pro-games.store');
    Route::get('/pro-games/{proGame}', [ProGameController::class, 'show'])->name('pro-games.show');
    Route::get('/pro-games/{proGame}/edit', [ProGameController::class, 'edit'])->name('pro-games.edit');
    Route::put('/pro-games/{proGame}', [ProGameController::class, 'update'])->name('pro-games.update');
    Route::delete('/pro-games/{proGame}', [ProGameController::class, 'destroy'])->name('pro-games.destroy');

    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/lessons/{lesson}', [CourseController::class, 'lesson'])->name('courses.lesson');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [CourseController::class, 'complete'])->name('courses.lesson.complete');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('puzzles', [PuzzleController::class, 'index'])->name('puzzles.index');
        Route::get('puzzles/create', [PuzzleController::class, 'create'])->name('puzzles.create');
        Route::post('puzzles', [PuzzleController::class, 'store'])->name('puzzles.store');
        Route::get('puzzles/{puzzle}/play', [PuzzleController::class, 'play'])->name('puzzles.play');
        Route::get('puzzles/{puzzle}/edit', [PuzzleController::class, 'edit'])->name('puzzles.edit');
        Route::put('puzzles/{puzzle}', [PuzzleController::class, 'update'])->name('puzzles.update');
        Route::delete('puzzles/{puzzle}', [PuzzleController::class, 'destroy'])->name('puzzles.destroy');

        Route::get('courses', [AdminCourseController::class, 'index'])->name('courses.index');
        Route::get('courses/create', [AdminCourseController::class, 'create'])->name('courses.create');
        Route::post('courses', [AdminCourseController::class, 'store'])->name('courses.store');
        Route::get('courses/{course}', [AdminCourseController::class, 'show'])->name('courses.show');
        Route::get('courses/{course}/edit', [AdminCourseController::class, 'edit'])->name('courses.edit');
        Route::put('courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
        Route::delete('courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

        Route::get('courses/{course}/lessons/create', [AdminLessonController::class, 'create'])->name('lessons.create');
        Route::post('courses/{course}/lessons', [AdminLessonController::class, 'store'])->name('lessons.store');
        Route::get('courses/{course}/lessons/{lesson}/edit', [AdminLessonController::class, 'edit'])->name('lessons.edit');
        Route::put('courses/{course}/lessons/{lesson}', [AdminLessonController::class, 'update'])->name('lessons.update');
        Route::delete('courses/{course}/lessons/{lesson}', [AdminLessonController::class, 'destroy'])->name('lessons.destroy');
    });
});

require __DIR__.'/auth.php';
