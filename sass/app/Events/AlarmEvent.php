<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\MIS\Alarm;
use App\Models\Mongo\HistorianFormatData;

class AlarmEvent
{
    public $alarm;
    public $tenement_conn;
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 创建报警信息时触发事件，警报发送的是多用户，此处不发送事件通知  不继承ShouldBroadcast
     *
     * @return void
     */
    public function __construct(Alarm $alarm, $tenement_conn)
    {
        $this->alarm = $alarm;
        $this->tenement_conn = $tenement_conn;
    }
}
