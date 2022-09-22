<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     * send参数1：视图名称
     * send参数2：要传递给视图的数据信息
     * @return void
     */
    public function handle()
    {
        Mail::send('mail', ['name'=>'浔阳江头夜送客'], function($message){
            $message->from('ye_goodluck@aliyun.com', 'cat');
            $message->subject('大弦嘈嘈如急雨');
            $message->to($this->email);
        });
    }
}
