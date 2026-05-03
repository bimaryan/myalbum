<?php

namespace App\Events;

use App\Models\Photo; // <-- Import Model Photo
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <-- Wajib ada
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Tambahin "implements ShouldBroadcast" di sini
class PhotoUploaded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Bikin properti public buat payload
    public $photo;

    /**
     * Create a new event instance.
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast ke channel spesifik per album
        return [
            new Channel('album.'.$this->photo->album_id),
        ];
    }

    // (Opsional) Custom nama event
    public function broadcastAs()
    {
        return 'PhotoUploaded';
    }
}
