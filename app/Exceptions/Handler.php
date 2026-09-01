<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        ZktecoTokenExpiredException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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

        $this->renderable(function (ZktecoTokenExpiredException $e, $request) {
            return ZktecoTokenExpiredException::logoutAndRespond($request, $e->getMessage());
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            return ZktecoTokenExpiredException::logoutAndRespond($request);
        });

        $this->renderable(function (TokenMismatchException $e, $request) {
            return ZktecoTokenExpiredException::logoutAndRespond($request);
        });
    }
}
