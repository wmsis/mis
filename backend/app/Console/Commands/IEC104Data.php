<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\IEC104DataJob;
use Log;

class IEC104Data extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:iec104data {--date=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'collect iec104data';

    protected $sock = null;
    protected $Tx = 0;
    protected $Rx = 0;
    protected $is_over = true;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $info_list = [];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $optionDate = $this->option('date');
        if($optionDate != 'default'){
            $date = $optionDate;
        }
        else{
            $date = date('Y-m-d');
        }
        Log::info('0000000000000000000');
        $this->iec104();

        //dispatch(new IEC104DataJob($date));
        return 0;
    }

    private function iec104(){
        $host = '127.0.0.1';
        $port = 2404;
        if(($this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === FALSE)
        {
            Log::info('初始化socket资源错误');
            exit('初始化socket资源错误: ' . socket_strerror(socket_last_error($this->sock)));
        }

        if(socket_connect($this->sock, $host, $port) === FALSE)
        {
            Log::info('连接socket失败');
            exit('连接socket失败: ' . socket_strerror(socket_last_error($this->sock)));
        }

        $data = '';
        // 循环读取指定长度的服务器响应数据
        $is_begin = true;
        while($is_over)
        {
            if($is_begin){
                # 1、发送启动帧，首次握手（U帧）680407000000
                $cmd = "68 04 07 00 00 00";
                $this->build_packet_and_sent($cmd, 'U');
                $is_begin = false;
                Log::info('=========发送启动帧68 04 07 00 00 00=========');
            }
            $receiveStr = socket_read($this->sock, 1024);
            $receiveStrHex = bin2hex($receiveStr); // 将2进制数据转换成16进制
            Log::info('000000000000');
            Log::info($receiveStrHex);
        }
        Log::info('接收消息成功');

        socket_close($this->sock);
    }

    private function build_packet_and_sent($sendStr, $type='S'){
        $sendStrArray = str_split(str_replace(' ', '', $sendStr), 2); // 将16进制数据转换成两个一组的数组
        for ($j = 0; $j < count($sendStrArray); $j++) {
            socket_write($this->sock, chr(hexdec($sendStrArray[$j]))); // 逐组数据发送
        }
    }

    //十进制接收序号
    private function setRx(){
        $this->Rx = $this->Rx+1;
        if ($this->Rx > 65534){
            $this->Rx = 1;
        }
    }

    //十进制发送序号
    private function setTx(){
        $this->Tx = $this->Tx + 1;
        if ($this->Tx > 65534){
            $this->Tx = 1;
        }
        return $this->Tx;
    }

    #发送序号重新组合
    private function getHexTx(){
        // byte_tx = bin($this->$Tx<<1)[2:]  #转换为2进制
        // str_len = len(byte_tx)
        // if str_len < 16:
        //     diff = 16 - str_len   #补0个数
        //
        // zero_str = ''
        // for i in range(diff):
        //     zero_str = zero_str + '0'
        // final_str = zero_str + byte_tx   #16位，不足补0
        //
        // max_bin = final_str[0:8] #高8位
        // low_bin = final_str[8:]  #低8位
        //
        // low_sixteen = (hex(int(low_bin, 2)))[2:]  #低8位转为16进制
        // max_sixteen = (hex(int(max_bin, 2)))[2:]  #高8位转为16进制
        // if len(low_sixteen) != 2:
        //     low_sixteen = '0' + low_sixteen
        // if len(max_sixteen) != 2:
        //     max_sixteen = '0' + max_sixteen
        // grp = low_sixteen + ' ' + max_sixteen
        // #print(grp)
        // return grp;
    }

    #接收序号重新组合
    private function getHexRx(){
        // byte_rx = bin($this-> Rx<<1)[2:]  #转换为2进制
        // str_len = len(byte_rx)
        // if str_len < 16:
        //     diff = 16 - str_len   #补0个数
        //
        // zero_str = ''
        // for i in range(diff):
        //     zero_str = zero_str + '0'
        // final_str = zero_str + byte_rx   #16位，不足补0
        //
        // max_bin = final_str[0:8] #高8位
        // low_bin = final_str[8:]  #低8位
        //
        // low_sixteen = (hex(int(low_bin, 2)))[2:]  #低8位转为16进制
        // max_sixteen = (hex(int(max_bin, 2)))[2:]  #高8位转为16进制
        // if len(low_sixteen) != 2:
        //     low_sixteen = '0' + low_sixteen
        // if len(max_sixteen) != 2:
        //     max_sixteen = '0' + max_sixteen
        // grp = low_sixteen + ' ' + max_sixteen
        // #print(grp)
        // return grp;
    }

    #S帧回调
    private function s_frame($hex_str){
    }

    #U帧回调
    private function u_frame($hex_str){
        if ($hex_str == '680443000000'){
            #接收U帧 测试帧  超过一定时间没有下发和上报时
            $cmd = '68 04 83 00 00 00';  #应答U帧
            $this->build_packet_and_sent($cmd, 'U');
            Log::info('======收到测试帧，应答U帧=======');
        }
        elseif($hex_str == '68040b000000'){
            Log::info('=========收到启动确认帧68 04 0b 00 00 00==========');
            #3、68（启动符）0E（长度）04  00（发送序号）0E  00（接收序号）65（类型标示）01（可变结构限定词）06  00（传输原因）01  00（公共地址）00 00 00（信息体地址）45（QCC）
            $tx = $this->getHexTx();  #发送序号
            $rx = $this->getHexRx();  #接收序号
            $cmd = '68 0e ' . $tx . ' ' . $rx . ' 65 01 06 00 01 00 00 00 00 45';
            Log::info('=========发送电度总召帧' + $cmd + '==========');
            $this->build_packet_and_sent($cmd, 'I');
        }
        else{
            //
        }
    }

    #I帧回调
    private function i_frame($hex_str){
        // $this-> setRx(); #设置接收序号
        // len = hex_str[2:4]  #长度
        // type = hex_str[12:14] #类型
        // cause = hex_str[16:20] #原因
        // if len == '0e' and type == '65' and cause == '0700':
        //     #接收电度总召唤确认
        //     #68（启动符）0E（长度）10  00（发送序号）06  00（接收序号）65（类型标示）01（可变结构限定词）07  00（传输原因）01  00（公共地址）00 00 00（信息体地址）45（QCC）
        //     Log::info('======接收电度总召唤确认=========')
        //     # rx = $this-> getHexRx()  #接收序号
        //     # cmd = '68 04 01 00 ' + rx  #应答S帧
        //     # packet = $this-> buildpacket(cmd, 'S')
        //     # print('========应答S帧=========>' + cmd)
        //     # $this-> send(packet, 'S')
        //     # time.sleep(0.2)
        // elif len == '0e' and type == '65' and cause == '0a00':
        //     #接收结束电度总召唤帧
        //     #68（启动符）0E（长度）14  00（发送序号）06  00（接收序号）65（类型标示）01（可变结构限定词）0a  00（传输原因）01  00（公共地址）00 00 00（信息体地址）45（QCC）
        //     Log::info('======接收结束电度总召唤帧=========')
        //     rx = $this-> getHexRx()  #接收序号
        //     cmd = '68 04 01 00 ' + rx  #应答S帧
        //     packet = $this-> buildpacket(cmd, 'S')
        //     print('========应答S帧=========>' + cmd)
        //     $this-> send(packet, 'S')
        //     time.sleep(0.5)
        //     $this-> is_over = True
        //     $this-> quit()
        //     $this-> submit()  #收到电度总召结束帧后再提交数据到服务器
        // elif type == '0f':
        //     Log::info('======接收电度数据=========')
        //     $this-> parse_data(hex_str)
        //
        //     // rx = $this-> getHexRx()  #接收序号
        //     // cmd = '68 04 01 00 ' + rx  #应答S帧
        //     // packet = $this-> buildpacket(cmd, 'S')
        //     // print('========应答S帧=========>' + cmd)
        //     // $this-> send(packet, 'S')
        //     // time.sleep(0.2)
    }

    #解析报文
    private function parse_data($hex_str){
    }

}
