<?php

namespace App\Events\Room;

use App\Room;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewRoom implements shouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public Room $room;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param Room $room
     */
    public function __construct(int $userId, Room $room)
    {
        $this->userId = $userId;
        $this->room = $room;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PresenceChannel
     */
    public function broadcastOn()
    {
        return new PresenceChannel('user.'.$this->userId);
    }

    public function broadcastAs()
    {
        return "room-new";
    }
}
