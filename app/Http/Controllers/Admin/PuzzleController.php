<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePuzzleRequest;
use App\Http\Requests\UpdatePuzzleRequest;
use App\Models\Puzzle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PuzzleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            abort_unless($request->user() && $request->user()->is_admin, 403);

            return $next($request);
        });
    }

    public function index(): View
    {
        return view('admin.puzzles.index', [
            'puzzles' => Puzzle::query()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.puzzles.create');
    }

    public function store(StorePuzzleRequest $request): RedirectResponse
    {
        Puzzle::create(array_merge($request->validated(), ['user_id' => auth()->id()]));

        return redirect()->route('admin.puzzles.index')->with('success', 'Saved.');
    }

    public function play(Puzzle $puzzle): View
    {
        return view('admin.puzzles.play', compact('puzzle'));
    }

    public function edit(Puzzle $puzzle): View
    {
        return view('admin.puzzles.edit', compact('puzzle'));
    }

    public function update(UpdatePuzzleRequest $request, Puzzle $puzzle): RedirectResponse
    {
        $puzzle->update($request->validated());

        return redirect()->route('admin.puzzles.index')->with('success', 'Updated.');
    }

    public function destroy(Puzzle $puzzle): RedirectResponse
    {
        $puzzle->delete();

        return redirect()->back()->with('success', 'Deleted.');
    }
}
