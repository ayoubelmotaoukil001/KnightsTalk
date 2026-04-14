<?php

namespace App\Http\Controllers;

use App\Models\Puzzle;

class PuzzleController extends Controller
{
    public function index()
    {
        $puzzles = Puzzle::latest()->get();

        return view('puzzles.index', compact('puzzles'));
    }

    public function play(Puzzle $puzzle)
    {
        return view('puzzles.play', compact('puzzle'));
    }
}
