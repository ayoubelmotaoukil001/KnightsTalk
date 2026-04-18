<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user() && $request->user()->is_admin, 403);
            return $next($request);
        });
    }

    public function create(Course $course)
    {
        return view('admin.lessons.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'moves_sequence' => 'required|string',
            'moves_data' => 'required|string',
        ]);

        $course->lessons()->create([
            'title' => $request->title,
            'moves_sequence' => $request->moves_sequence,
            'move_descriptions' => json_decode($request->moves_data, true),
            'order' => $course->lessons()->max('order') + 1,
        ]);

        return redirect()->route('admin.courses.show', $course)->with('success', 'Lesson saved.');
    }

    public function edit(Course $course, Lesson $lesson)
    {
        return view('admin.lessons.edit', compact('course', 'lesson'));
    }

    public function update(Request $request, Course $course, Lesson $lesson)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'moves_sequence' => 'required|string',
            'moves_data' => 'required|string',
        ]);

        $lesson->update([
            'title' => $request->title,
            'moves_sequence' => $request->moves_sequence,
            'move_descriptions' => json_decode($request->moves_data, true),
        ]);

        return redirect()->route('admin.courses.show', $course)->with('success', 'Lesson updated.');
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('admin.courses.show', $course)->with('success', 'Lesson deleted.');
    }
}
