<?php

namespace App\Events;

use App\Models\File;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileProcessingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $file;

    public function __construct($id)
    {
        $this->file = File::where("id", $id)->first();
    }

    public function broadcastOn()
    {
        return ['file_processing-channel'];
    }

    public function broadcastAs()
    {
        return 'file_processing-event';
    }
}
