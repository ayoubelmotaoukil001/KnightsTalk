<?php

namespace App\Http\Controllers;

use App\Models\Puzzle;
use Illuminate\Support\Facades\DB;

class PuzzleController extends Controller
{
    public function index()
    {
        $puzzles = Puzzle::latest()->get();
        $userId = auth()->id();

        $completedIds = DB::table('puzzle_user')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->pluck('puzzle_id')
            ->toArray();

        $total = $puzzles->count();
        $solved = count($completedIds);
        $percent = $total > 0 ? round(($solved / $total) * 100) : 0;

        return view('puzzles.index', compact('puzzles', 'completedIds', 'total', 'solved', 'percent'));
    }

    public function play(Puzzle $puzzle)
    {
        $row = DB::table('puzzle_user')
            ->where('user_id', auth()->id())
            ->where('puzzle_id', $puzzle->id)
            ->first();

        $alreadySolved = $row && $row->status === 'completed';
        $attempts = $row ? $row->attempts : 0;

        return view('puzzles.play', compact('puzzle', 'alreadySolved', 'attempts'));
    }

    public function complete(Puzzle $puzzle)
    {
        $userId = auth()->id();
        $exists = DB::table('puzzle_user')->where('user_id', $userId)->where('puzzle_id', $puzzle->id)->first();

        if ($exists) {
            DB::table('puzzle_user')->where('user_id', $userId)->where('puzzle_id', $puzzle->id)
                ->update(['status' => 'completed', 'updated_at' => now()]);
        } else {
            DB::table('puzzle_user')->insert([
                'user_id' => $userId, 'puzzle_id' => $puzzle->id,
                'status' => 'completed', 'attempts' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function attempt(Puzzle $puzzle)
    {
        $userId = auth()->id();
        $exists = DB::table('puzzle_user')->where('user_id', $userId)->where('puzzle_id', $puzzle->id)->first();

        if (!$exists) {
            DB::table('puzzle_user')->insert([
                'user_id' => $userId, 'puzzle_id' => $puzzle->id,
                'status' => 'failed', 'attempts' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        } elseif ($exists->status !== 'completed') {
            DB::table('puzzle_user')->where('user_id', $userId)->where('puzzle_id', $puzzle->id)
                ->update(['attempts' => $exists->attempts + 1, 'updated_at' => now()]);
        }

        return response()->json(['success' => true]);
    }
}
