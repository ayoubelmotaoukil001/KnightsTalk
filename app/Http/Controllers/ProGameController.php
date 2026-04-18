<?php

namespace App\Http\Controllers;

use App\Models\ProGame;
use Illuminate\Http\Request;

class ProGameController extends Controller
{
    public function index()
    {
        $games = ProGame::latest()->get();
        return view('pro-games.index', compact('games'));
    }

    public function show(ProGame $proGame)
    {
        return view('pro-games.show', compact('proGame'));
    }

    public function create()
    {
        abort_unless(auth()->user()->is_admin, 403);
        return view('pro-games.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->is_admin, 403);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'moves_data' => 'required|string',
        ]);

        $movesData = json_decode($request->input('moves_data'), true);

        ProGame::create([
            'title' => $request->input('title'),
            'moves_data' => $movesData,
        ]);

        return redirect()->route('pro-games.index')->with('success', 'Pro Game saved successfully!');
    }

    public function edit(ProGame $proGame)
    {
        abort_unless(auth()->user()->is_admin, 403);
        return view('pro-games.edit', compact('proGame'));
    }

    public function update(Request $request, ProGame $proGame)
    {
        abort_unless(auth()->user()->is_admin, 403);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'moves_data' => 'required|string',
        ]);

        $movesData = json_decode($request->input('moves_data'), true);

        $proGame->update([
            'title' => $request->input('title'),
            'moves_data' => $movesData,
        ]);

        return redirect()->route('pro-games.show', $proGame)->with('success', 'Pro Game updated!');
    }

    public function destroy(ProGame $proGame)
    {
        abort_unless(auth()->user()->is_admin, 403);
        $proGame->delete();
        return redirect()->route('pro-games.index')->with('success', 'Pro Game deleted.');
    }

}
