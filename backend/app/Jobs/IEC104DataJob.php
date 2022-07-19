<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class IEC104DataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $date;
    public $tries = 3;

    /**
     * 导出生产指标考核表
     *
     * @return void
     */
    public function __construct($date=null)
    {
        Log::info('1111111111111');
        $this->date = $date ? $date : date('Y-m-d');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        #ini_set('memory_limit', -1);
        Log::info('2222222222222');
        $host = '127.0.0.1';
        $port = 2404;
        if(($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === FALSE)
        {
            Log::info('初始化socket资源错误');
            exit('初始化socket资源错误: ' . socket_strerror(socket_last_error($sock)));
        }

        if(socket_connect($sock, $host, $port) === FALSE)
        {
            Log::info('连接socket失败');
            exit('连接socket失败: ' . socket_strerror(socket_last_error($sock)));
        }

        $data = '';
        // 循环读取指定长度的服务器响应数据
        while($response = socket_read($sock, 4))
        {
            $data .= $response;
        }
        Log::info('接收消息成功');
        socket_write($sock, '接收消息成功');
        echo $data . PHP_EOL;

        socket_close($sock);
    }
}
