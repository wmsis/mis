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
use UtilService;

class Handler extends ExceptionHandler
{
    const AJAX_INVALID = 20001;
    const AJAX_UNAUTH = 30001;
    const AJAX_MODEL_NOT_FOUND = 40001;
    const AJAX_NOT_FOUND = 50001;
    const AJAX_METHOD_NOT_ALLOWED = 60001;
    const AJAX_TOKEN_BLACKLISTED = 70001;
    const AJAX_TOKEN_INVALID = 80001;
    const AJAX_TOKEN_NOT_PROVIDE = 90001;
    const AJAX_METHOD_ERROR = 20002;
    const AJAX_CONN_ERROR = 30002;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof MethodNotAllowedException){

        }
        elseif($exception instanceof MethodNotAllowedHttpException){
            $res = UtilService::format_data(self::AJAX_METHOD_ERROR, '请求方法错误', '');
            return response()->json($res);
        }
        elseif($exception instanceof NotFoundHttpException){

        }
        elseif($exception instanceof ModelNotFoundException){

        }
        elseif($exception instanceof ValidationException){
            $msg = $exception->validator->errors()->first();
            $res = UtilService::format_data(self::AJAX_INVALID, $msg, '');
            return response()->json($res);
        }
        elseif($exception instanceof UnauthorizedHttpException){
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
            $res = UtilService::format_data(self::AJAX_TOKEN_INVALID, 'token已失效', '');
            return response()->json($res);
        }
        elseif($exception instanceof JWTException){
            $res = UtilService::format_data(self::AJAX_TOKEN_NOT_PROVIDE, 'token异常', '');
            return response()->json($res);
        }
        elseif($exception instanceof ConnectException){
            $res = UtilService::format_data(self::AJAX_CONN_ERROR, '连接异常', '');
            return response()->json($res);
        }

        return parent::render($request, $exception);
    }
}
