<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\ProGame;
use App\Models\Puzzle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        // All lesson IDs this user completed
        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $userId)->where('completed', true)
            ->pluck('lesson_id')->toArray();

        // Courses progress (only courses user has touched)
        $courseProgress = [];
        $courses = Course::withCount('lessons')->having('lessons_count', '>', 0)->get();

        foreach ($courses as $course) {
            $lessonIds = $course->lessons()->pluck('id')->toArray();
            $done = count(array_intersect($lessonIds, $completedLessonIds));
            if ($done === 0) continue;

            $courseProgress[] = [
                'title' => $course->title,
                'url' => route('courses.show', $course),
                'completed' => $done,
                'total' => $course->lessons_count,
                'percent' => round(($done / $course->lessons_count) * 100),
            ];
        }

        // Puzzles
        $totalPuzzles = Puzzle::count();
        $solvedPuzzles = DB::table('puzzle_user')
            ->where('user_id', $userId)->where('status', 'completed')->count();
        $totalAttempts = (int) DB::table('puzzle_user')
            ->where('user_id', $userId)->sum('attempts');
        $puzzlePercent = $totalPuzzles > 0 ? round(($solvedPuzzles / $totalPuzzles) * 100) : 0;

        // Last lesson
        $lastLessonRow = DB::table('lesson_user')
            ->where('user_id', $userId)->orderByDesc('updated_at')->first();
        $lastLesson = $lastLessonRow ? Lesson::with('course')->find($lastLessonRow->lesson_id) : null;

        // Last pro game
        $lastProGame = ProGame::latest()->first();

        return view('dashboard', compact(
            'user', 'courseProgress',
            'totalPuzzles', 'solvedPuzzles', 'totalAttempts', 'puzzlePercent',
            'lastLesson', 'lastProGame'
        ));
    }
}
