<?php

/**
 * 微信支付
 */

return [
    'mchid' => env('WXPAY_MCHID',"1501166211"),
    'WxPayKey' => env('WXPAY_KEY',"qwertyuiopasdfghjklmnbvcxz123467"),
    'SSLCERT_PATH' => dirname(dirname(__FILE__)).'/cert/apiclient_cert.pem',
    'SSLKEY_PATH' => dirname(dirname(__FILE__)).'/cert/apiclient_key.pem'
];