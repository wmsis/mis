<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\QueryException;
use App\Models\SIS\Electricity;
use Log;

class IEC104DataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $tenement_conn; //租户连接
    protected $local_data_table; //本地保存的MongoDB原始数据集合
    protected $map; //本地保存的格式化后的数据集合
    protected $cfgdb;//数据库配置信息
    public $tries = 3;
    protected $sock = null;
    protected $Tx = 0;
    protected $Rx = 0;
    protected $is_over = false;
    protected $info_list = [];

    /**
     * 导出生产指标考核表
     *
     * @return void
     */
    public function __construct($params=null)
    {
        $this->tenement_conn = $params && isset($params['tenement_conn']) ? $params['tenement_conn'] : '';
        $this->local_data_table = $params && isset($params['local_data_table']) ? $params['local_data_table'] : '';
        $this->map = $params && isset($params['map']) ? $params['map'] : '';
        $this->cfgdb = $params && isset($params['cfgdb']) ? $params['cfgdb'] : '';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->start('master');
        } catch (Exception $e) {
            $this->start('slave');
        }
    }

    //开始发送和接收报文
    private function start($type='master'){
        if($type == 'master'){
            $host = $this->cfgdb['master_ip'];
        }
        else{
            $host = $this->cfgdb['slave_ip'];
            if(!$host){
                return false;
            }
        }
        $port = 2404;
        if(($this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) !== FALSE)
        {
            if(socket_connect($this->sock, $host, $port) !== FALSE)
            {
                $data = '';
                // 循环读取指定长度的服务器响应数据
                $is_begin = true;
                while(!$this->is_over)
                {
                    if($is_begin){
                        # 1、发送启动帧，首次握手（U帧）680407000000
                        $cmd = "68 04 07 00 00 00";
                        $this->build_packet_and_sent($cmd, 'U');
                        $is_begin = false;
                        //Log::info('=========发送启动帧68 04 07 00 00 00=========');
                    }
                    $receiveStr = socket_read($this->sock, 1024);
                    $receiveStrHex = bin2hex($receiveStr); // 将2进制数据转换成16进制
                    //Log::info('收到16进制报文');
                    //Log::info($receiveStrHex);

                    if (strlen($receiveStrHex) == 12){
                        #U帧和S帧
                        if (strpos($receiveStrHex, '68040100') != false){
                            #S帧
                            $this->s_frame($receiveStrHex);
                        }
                        else{
                            #U帧
                            $this->u_frame($receiveStrHex);
                        }
                    }
                    else{
                        #I帧
                        $this->i_frame($receiveStrHex);
                    }
                }
                //Log::info('接收消息结束');

                socket_close($this->sock);
            }
        }
    }

    //创建发送包
    private function build_packet_and_sent($sendStr, $type='S'){
        usleep(100000); //微秒数
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

    //发送序号重新组合
    private function getHexTx(){
        $byte_tx = decbin($this->Tx<<1);  #十进制转换为2进制
        $str_len = strlen($byte_tx);
        if($str_len < 16){
            $diff = 16 - $str_len;   #补0个数
        }

        $zero_str = '';
        for ($i=0; $i<$diff; $i++){
            $zero_str = $zero_str . '0';
        }
        $final_str = $zero_str . $byte_tx;   #16位，不足补0

        $high_bin = substr($final_str, 0, 8); #高8位
        $low_bin = substr($final_str, 8);  #低8位

        $low_sixteen = dechex(bindec($low_bin));  #低8位转为16进制
        $high_sixteen = dechex(bindec($high_bin));  #高8位转为16进制
        if (strlen($low_sixteen) != 2){
            $low_sixteen = '0' . $low_sixteen;
        }
        if (strlen($high_sixteen) != 2){
            $high_sixteen = '0' . $high_sixteen;
        }
        $grp = $low_sixteen . ' ' . $high_sixteen;
        #print($grp)
        return $grp;
    }

    //接收序号重新组合
    private function getHexRx(){
        $byte_rx = decbin($this->Rx<<1);  #十进制转换为2进制
        $str_len = strlen($byte_rx);
        if($str_len < 16){
            $diff = 16 - $str_len;   #补0个数
        }

        $zero_str = '';
        for ($i=0; $i<$diff; $i++){
            $zero_str = $zero_str . '0';
        }
        $final_str = $zero_str . $byte_rx;   #16位，不足补0

        $high_bin = substr($final_str, 0, 8); #高8位
        $low_bin = substr($final_str, 8);  #低8位

        $low_sixteen = dechex(bindec($low_bin));  #低8位转为16进制
        $high_sixteen = dechex(bindec($high_bin));  #高8位转为16进制
        if (strlen($low_sixteen) != 2){
            $low_sixteen = '0' . $low_sixteen;
        }
        if (strlen($high_sixteen) != 2){
            $high_sixteen = '0' . $high_sixteen;
        }
        $grp = $low_sixteen . ' ' . $high_sixteen;
        #print($grp)
        return $grp;
    }

    //S帧回调
    private function s_frame($hex_str){
    }

    //U帧回调
    private function u_frame($hex_str){
        if ($hex_str == '680443000000'){
            #接收U帧 测试帧  超过一定时间没有下发和上报时
            $cmd = '68 04 83 00 00 00';  #应答U帧
            $this->build_packet_and_sent($cmd, 'U');
            //Log::info('======收到测试帧，应答U帧 ' . $cmd . '=======');
        }
        elseif($hex_str == '68040b000000'){
            //Log::info('=========收到启动确认帧68 04 0b 00 00 00==========');
            #3、68（启动符）0E（长度）04  00（发送序号）0E  00（接收序号）65（类型标示）01（可变结构限定词）06  00（传输原因）01  00（公共地址）00 00 00（信息体地址）45（QCC）
            $tx = $this->getHexTx();  #发送序号
            $rx = $this->getHexRx();  #接收序号
            $cmd = '68 0e ' . $tx . ' ' . $rx . ' 65 01 06 00 01 00 00 00 00 45';
            //Log::info('=========发送电度总召帧' . $cmd . '==========');
            $this->build_packet_and_sent($cmd, 'I');
        }
        else{
            //
        }
    }

    //I帧回调
    private function i_frame($hex_str){
        $this->setRx(); #设置接收序号
        $len = substr($hex_str, 2, 2);  #长度
        $type = substr($hex_str, 12, 2); #类型
        $cause = substr($hex_str, 16, 4); #原因
        if ($len == '0e' && $type == '65' && $cause == '0700'){
            #接收电度总召唤确认
            #68（启动符）0E（长度）10  00（发送序号）06  00（接收序号）65（类型标示）01（可变结构限定词）07  00（传输原因）01  00（公共地址）00 00 00（信息体地址）45（QCC）
            //Log::info('======接收电度总召唤确认=========');
            # $rx = $this->getHexRx();  #接收序号
            # $cmd = '68 04 01 00 ' . $rx;  #应答S帧
            # $this->build_packet_and_sent($cmd, 'S');
            # Log::info('========应答S帧=========>' . $cmd);
            # usleep(200000);
        }
        elseif ($len == '0e' && $type == '65' && $cause == '0a00'){
            #接收结束电度总召唤帧
            #68（启动符）0E（长度）14  00（发送序号）06  00（接收序号）65（类型标示）01（可变结构限定词）0a  00（传输原因）01  00（公共地址）00 00 00（信息体地址）45（QCC）
            //Log::info('======接收结束电度总召唤帧=========');
            $rx = $this->getHexRx();  #接收序号
            $cmd = '68 04 01 00 ' . $rx;  #应答S帧
            $this->build_packet_and_sent($cmd, 'S');
            //Log::info('========应答S帧=========>' . $cmd);
            $this->is_over = true;

            sleep(1);
            $this->submit();  #收到电度总召结束帧后再提交数据到服务器
        }
        elseif ($type == '0f'){
            //Log::info('======接收电度数据=========');
            $this->parse_data($hex_str);

            // $rx = $this->getHexRx();  #接收序号
            // $cmd = '68 04 01 00 ' . $rx;  #应答S帧
            // $this->build_packet_and_sent($cmd, 'S');
            // Log::info('========应答S帧=========>' . $cmd);
            // usleep(200000);
        }
    }

    //16进制报文转10进制
    private function info_hex_to_int($params){
        $i = 0;
        $hex_str = '';
        while ($i < strlen($params)){
            $hex_str = substr($params, $i, 2) . $hex_str;
            $i = $i + 2;
        }
        return hexdec($hex_str);
    }

    //解析报文
    private function parse_data($hex_str){
        $info_len = substr($hex_str, 2, 2);  #长度
        $type = substr($hex_str, 12, 2); #类型
        $cause = substr($hex_str, 16, 4); #原因
        if ($type == '0f'){
            $data = substr($hex_str, 24);
            $i = 0;
            while ($i < strlen($data)){
                $addr = $this->info_hex_to_int(substr($data, $i, 6));
                $value = $this->info_hex_to_int(substr($data, $i+6, 8));  #信息站四字节
                $quality = hexdec(substr($data, $i+6+8, 2));  #信息描述1字节

                $this->info_list[$addr] = [
                    'addr'=> $addr,
                    'value'=> $value,
                    'quality'=> $quality
                ];
                $i = $i + 16;
            }
        }
    }

    private function submit(){
        $tags = $this->map;
        $cn_names = [];
        foreach ($tags as $key => $item) {
            $cn_names[] = array(
                'name'=> $item['cn_name'],
                'electricity_map_id'=> $item['id'],
                'factor'=> $item['func'],
                'rate'=> $item['rate'] ? $item['rate'] : 1
            );
        }

        ksort($this->info_list);
        $sorted_values = $this->info_list;
        $params = array();

        if(count($sorted_values) == count($tags)){
            $i = 0;
            foreach ($sorted_values as $key => $item) {
                $params[] = array(
                    "electricity_map_id" => $cn_names[$i]['electricity_map_id'],
                    "value" => $item['value'],
                    "actual_value" => $item['value'] * $cn_names[$i]['factor'] * $cn_names[$i]['rate']
                );
                $i++;
            }

            try {
                $electricity = (new Electricity())->setConnection($this->tenement_conn)->setTable($this->local_data_table);

                foreach ($params as $key => $value) {
                    $params[$key]['created_at'] = date('Y-m-d H:i:s');
                    $params[$key]['updated_at'] = date('Y-m-d H:i:s');
                }
                $electricity->insertMany($params);
                //Log::info('=========操作成功=========');
            } catch (QueryException $e) {

            }
        }
        else{
            //Log::info('数据匹配不上');
        }
    }
}
