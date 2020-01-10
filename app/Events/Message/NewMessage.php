<?php

namespace App\Events\Message;

use App\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public Message $message;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param Message $message
     */
    public function __construct(int $userId, Message $message)
    {
        $this->userId = $userId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PresenceChannel
     */
    public function broadcastOn()
    {
        return new PresenceChannel('room.'.$this->message->room_id);
    }

    public function broadcastAs()
    {
        return "message-new";
    }
}
