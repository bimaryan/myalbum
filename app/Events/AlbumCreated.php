<?php

namespace App\Events;

use App\Models\Album; // <-- Jangan lupa import Model-nya
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // <-- Wajib ada
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Tambahin "implements ShouldBroadcast" di sini
class AlbumCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Bikin properti public, otomatis jadi JSON payload pas di-broadcast
    public $album;

    /**
     * Create a new event instance.
     */
    public function __construct(Album $album)
    {
        // Masukin data dari controller ke properti public
        $this->album = $album;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        // Pake Channel publik (Channel) bukan PrivateChannel biar gampang testing
        return [
            new Channel('albums'),
        ];
    }

    // (Opsional) Kalau lu mau custom nama event-nya pas ditangkep Flutter
    public function broadcastAs()
    {
        return 'AlbumCreated';
    }
}
