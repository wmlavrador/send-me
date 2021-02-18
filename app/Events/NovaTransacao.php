<?php

namespace App\Events;

use Illuminate\Broadcasting\{Channel, InteractsWithSockets};
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NovaTransacao implements ShouldBroadcast {
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $message;
    public $user_id;

    public function __construct($message, $user_id) {
        $this->message = $message;
        $this->user_id = $user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() {
        return new Channel('notificacoes.'.$this->user_id);
    }

    public function broadcastWith() {
        return [
            'message' => $this->message,
        ];
    }
}
