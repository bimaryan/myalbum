<?php

namespace App\Events;

use App\Models\Photo; // <-- Import Model Photo
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <-- Wajib ada
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Broadcast langsung tanpa antrian queue
class PhotoUploaded implements ShouldBroadcastNow
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
        // Broadcast ke channel spesifik per album (pakai SLUG agar cocok dengan Flutter)
        return [
            new Channel('album.'.$this->photo->album->slug),
        ];
    }

    // (Opsional) Custom nama event
    public function broadcastAs()
    {
        return 'PhotoUploaded';
    }
}
