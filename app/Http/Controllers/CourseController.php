<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('lessons')->latest()->get();
        $userId = auth()->id();

        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $userId)
            ->where('completed', true)
            ->pluck('lesson_id')
            ->toArray();

        foreach ($courses as $course) {
            $lessonIds = $course->lessons()->pluck('id')->toArray();
            $done = count(array_intersect($lessonIds, $completedLessonIds));
            $course->completed_count = $done;
            $course->progress_percentage = $course->lessons_count > 0
                ? round(($done / $course->lessons_count) * 100)
                : 0;
        }

        return view('courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load('lessons');
        $userId = auth()->id();

        $completedIds = DB::table('lesson_user')
            ->where('user_id', $userId)
            ->where('completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $total = $course->lessons->count();
        $completed = $course->lessons->filter(fn($l) => in_array($l->id, $completedIds))->count();
        $progress_percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return view('courses.show', compact('course', 'completedIds', 'progress_percentage', 'completed', 'total'));
    }

    public function lesson(Course $course, Lesson $lesson)
    {
        $nextLesson = $course->lessons()->where('order', '>', $lesson->order)->first();

        $isCompleted = DB::table('lesson_user')
            ->where('user_id', auth()->id())
            ->where('lesson_id', $lesson->id)
            ->where('completed', true)
            ->exists();

        return view('courses.lesson', compact('course', 'lesson', 'nextLesson', 'isCompleted'));
    }

    public function complete(Course $course, Lesson $lesson)
    {
        DB::table('lesson_user')->upsert(
            ['user_id' => auth()->id(), 'lesson_id' => $lesson->id, 'completed' => true, 'created_at' => now(), 'updated_at' => now()],
            ['user_id', 'lesson_id'],
            ['completed', 'updated_at']
        );

        return response()->json(['success' => true]);
    }
}
