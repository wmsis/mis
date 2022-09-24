<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\\Database\\QueryException;
use UtilService;
use Log;

class Handler extends ExceptionHandler
{
    const AJAX_INVALID = 20001;   //认证错误
    const AJAX_UNAUTH = 30001;    //非法用户
    const AJAX_METHOD_ERROR = 40001;
    const AJAX_CONN_ERROR = 50001;
    const AJAX_QUERY_ERROR = 60001;
    const AJAX_TOKEN_EXPIRED = 80001;
    const AJAX_TOKEN_BLACKLISTED = 80002;
    const AJAX_TOKEN_INVALID = 80003;
    const AJAX_TOKEN_NOT_PROVIDE = 80004;


    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if($exception instanceof UnauthorizedHttpException || $exception instanceof AuthenticationException){
            $res = UtilService::format_data(self::AJAX_UNAUTH, '非法用户', '');
            return response()->json($res);
        }
        elseif($exception instanceof TokenBlacklistedException){
            $res = UtilService::format_data(self::AJAX_TOKEN_BLACKLISTED, 'token已失效', '');
            return response()->json($res);
        }
        elseif($exception instanceof TokenInvalidException){
            $res = UtilService::format_data(self::AJAX_TOKEN_INVALID, 'token非法', '');
            return response()->json($res);
        }
        elseif($exception instanceof TokenExpiredException){
            $res = UtilService::format_data(self::AJAX_TOKEN_EXPIRED, 'token已过期', '');
            return response()->json($res);
        }
        elseif($exception instanceof JWTException){
            $res = UtilService::format_data(self::AJAX_TOKEN_NOT_PROVIDE, 'token异常', '');
            return response()->json($res);
        }
        elseif($exception instanceof MethodNotAllowedException){

        }
        elseif($exception instanceof MethodNotAllowedHttpException){
            $res = UtilService::format_data(self::AJAX_METHOD_ERROR, '请求方法错误', '');
            return response()->json($res);
        }
        elseif($exception instanceof NotFoundHttpException){

        }
        elseif($exception instanceof ModelNotFoundException){

        }
        elseif($exception instanceof QueryException){
            $res = UtilService::format_data(self::AJAX_QUERY_ERROR, '查询错误，数据库或未配置', '');
            return response()->json($res);
        }
        elseif($exception instanceof ValidationException){
            $msg = $exception->validator->errors()->first();
            $res = UtilService::format_data(self::AJAX_INVALID, $msg, '');
            return response()->json($res);
        }
        elseif($exception instanceof ConnectException){
            $res = UtilService::format_data(self::AJAX_CONN_ERROR, '连接异常', '');
            return response()->json($res);
        }

        return parent::render($request, $exception);
    }
}
