<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user() && $request->user()->is_admin, 403);
            return $next($request);
        });
    }

    public function index()
    {
        $courses = Course::withCount('lessons')->latest()->get();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,pro',
        ]);

        Course::create($request->only('title', 'description', 'level'));

        return redirect()->route('admin.courses.index')->with('success', 'Course created.');
    }

    public function show(Course $course)
    {
        $course->load('lessons');
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,pro',
        ]);

        $course->update($request->only('title', 'description', 'level'));

        return redirect()->route('admin.courses.show', $course)->with('success', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted.');
    }
}
