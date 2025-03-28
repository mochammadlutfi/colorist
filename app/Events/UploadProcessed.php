<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UploadProcessed implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $upload;

    public function __construct($upload)
    {
        $this->upload = $upload;
    }

    public function broadcastOn()
    {
        // dd($this->upload->user_id);
        // Gunakan channel private berdasarkan user_id
        return new PrivateChannel('uploads.' . $this->upload->user_id);
    }

    public function broadcastAs()
    {
        return 'upload.processed';
    }
}
