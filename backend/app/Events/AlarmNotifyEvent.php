<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\User;
use App\Http\Models\SIS\HistorianTag;
use App\Http\Models\Historian\AlarmRecord;

class AlarmNotifyEvent implements ShouldBroadcast
{
    public  $record;
    public  $user;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, AlarmRecord $record)
    {
        $this->record = $record;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('alarm-notify.' . $this->user->id);
    }

    public function broadcastWith()
    {
        $tag = HistorianTag::find($this->record->tag_id);
        return [
            'data' => 'broadcast data uid: '.$this->user->id,
            'record' => $this->record,
            'tag' => $tag
        ];
    }
}
