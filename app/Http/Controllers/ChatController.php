<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function index()
    {
        $messages = Message::with('user')->latest()->take(100)->get()->reverse();
        return view('chat.index', compact('messages'));
    }

    public function store(StoreMessageRequest $request)
    {
        $validated = $request->validated();
        $message = auth()->user()->messages()->create($validated);
        $message->load('user');
        broadcast(new MessageSent($message))->toOthers();

        if ($request->expectsJson()) {
            return response()->json($message);
        }

        return redirect()->route('chat.index');
    }

    public function show(Message $message)
    {
        return view('chat.show', compact('message'));
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('chat.index');
    }
}
