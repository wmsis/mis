<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    /**
     * 状态码
     * @var int|mixed
     */
    public $code = 200;

    /**
     * 错误具体信息
     * @var mixed|string
     */
    public $message = 'json';

    /**
     * 构造函数，接收关联数组
     * BaseException constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        parent::__construct();
        if (!is_array($params)) {
            return ;
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('message', $params)) {
            $this->message = $params['message'];
        }
    }
}
