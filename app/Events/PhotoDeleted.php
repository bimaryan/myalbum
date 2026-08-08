<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class PhotoDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public $photoId;
    public $albumSlug;

    /**
     * Create a new event instance.
     */
    public function __construct(int $photoId, string $albumSlug)
    {
        $this->photoId = $photoId;
        $this->albumSlug = $albumSlug;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('album.'.$this->albumSlug),
        ];
    }

    /**
     * Custom nama event.
     */
    public function broadcastAs()
    {
        return 'PhotoDeleted';
    }
}
