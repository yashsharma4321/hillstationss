<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sender;
    public $receiverId;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->sender = $message->sender;
        $this->receiverId = $message->receiver_id;
    }

    public function broadcastOn()
    {
        // Private channel for specific user
        return new PrivateChannel('chat.' . $this->receiverId);
    }
    public function broadcastAs()
    {
        return 'message.sent';
    }
    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'created_at' => $this->message->created_at->diffForHumans(),
            'timestamp' => $this->message->created_at->format('H:i:s'),
        ];
    }
}