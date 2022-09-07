<?php

namespace App\Services;
use JPush\Client as JPush;
use Log;

class JPushService{
    private $app_key;
    private $master_secret;

    public function __construct()
    {
        $this->app_key = config('jpush.app_key');
        $this->master_secret = config('jpush.master_secret');
    }

    public function send($params=null){
        Log::info('00000000000');
        $client = new JPush($this->app_key, $this->master_secret);
        $pusher = $client->push();
        $pusher->setPlatform('all');
        $pusher->addAllAudience();

        if(isset($params['message']) && isset($params['message']['message_content']) && isset($params['message']['options'])) {
            $pusher->message($params['message']['message_content'], $params['message']['options']);
        }

        if(isset($params['notification']) && isset($params['notification']['alert'])) {
            $pusher->setNotificationAlert($params['notification']['alert']);
        }

        if(isset($params['iosNotification']) && isset($params['iosNotification']['alert']) && isset($params['iosNotification']['options']) ){
            $pusher->iosNotification($params['iosNotification']['alert'], $params['iosNotification']['options']);
        }

        if(isset($params['androidNotification']) && isset($params['androidNotification']['alert']) && isset($params['androidNotification']['options'])) {
            $pusher->androidNotification($params['androidNotification']['alert'], $params['androidNotification']['options']);
        }

        if(isset($params['options']) && $params['options']) {
            $pusher->options($params['options']);
        }

        if(isset($params['smsMessage']) && $params['smsMessage']) {
            $pusher->setSmsMessage($params['smsMessage']);
        }

        try {
            Log::info('1111111111');
            $pusher->send();
            return 'SUCCESS';
        } catch (\JPush\Exceptions\APIConnectionException $e) {
            // try something here
            Log::info('22222222222');
            return 'FAIL';
        } catch (\JPush\Exceptions\APIRequestException $e) {
            // try something here
            Log::info('33333333333');
            Log:info(var_export($e->getMessage(), true));
            return 'FAIL';
        }
    }
}