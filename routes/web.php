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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
        Route::post('/profile/photo', 'uploadPhoto')->name('profile.photo');
    });

    Route::prefix('puzzles')->name('puzzles.')->controller(UserPuzzleController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{puzzle}/play', 'play')->name('play');
        Route::post('{puzzle}/complete', 'complete')->name('complete');
        Route::post('{puzzle}/attempt', 'attempt')->name('attempt');
    });

    Route::prefix('chat')->name('chat.')->controller(ChatController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::delete('{message}', 'destroy')->name('destroy');
     });

    Route::resource('pro-games', ProGameController::class);

    Route::prefix('courses')->name('courses.')->controller(CourseController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{course}', 'show')->name('show');
        Route::get('{course}/lessons/{lesson}', 'lesson')->name('lesson');
        Route::post('{course}/lessons/{lesson}/complete', 'complete')->name('lesson.complete');
    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::controller(PuzzleController::class)->group(function () {
            Route::get('puzzles/{puzzle}/play', 'play')->name('puzzles.play');
            Route::resource('puzzles', PuzzleController::class)->except(['show']);
        });

        Route::resource('courses', AdminCourseController::class);

        Route::prefix('courses/{course}/lessons')->name('lessons.')->controller(AdminLessonController::class)->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('{lesson}/edit', 'edit')->name('edit');
            Route::put('{lesson}', 'update')->name('update');
            Route::delete('{lesson}', 'destroy')->name('destroy');
        });
    });
});

require __DIR__.'/auth.php';
