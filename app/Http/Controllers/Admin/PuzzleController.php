<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePuzzleRequest;
use App\Models\Puzzle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PuzzleController extends Controller
{
    /**
     * Show all puzzles.
     */
    public function index(): View
    {
        $puzzles = Puzzle::query()->latest()->get();

        return view('admin.puzzles.index', compact('puzzles'));
    }

    /**
     * Show create puzzle form.
     */
    public function create(): View
    {
        return view('admin.puzzles.create');
    }

    /**
     * Save puzzle + its solution moves.
     */
    public function store(StorePuzzleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        Puzzle::create($validated);

        return redirect()
            ->route('admin.puzzles.index')
            ->with('success', 'Puzzle created successfully.');
    }

    /**
     * Delete puzzle (solutions removed via FK cascade).
     */
    public function destroy(Puzzle $puzzle): RedirectResponse
    {
        $puzzle->delete();

        return redirect()
            ->back()
            ->with('success', 'Puzzle deleted successfully.');
    }
}

