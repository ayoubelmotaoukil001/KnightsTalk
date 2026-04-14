<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\Message;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
        $this->message->load('user');
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('knighttsTalk'),
        ];
    }

    public function broadcastWith(): array
    {
        $photo = $this->message->user->profile_photo
            ? Storage::url($this->message->user->profile_photo)
            : null;

        return [
            'message' => [
                'id'        => $this->message->id,
                'content'   => $this->message->content,
                'user_id'   => $this->message->user_id,
                'photo_url' => $photo,
                'user'      => [
                    'name' => $this->message->user->name,
                ],
            ],
        ];
    }
}
