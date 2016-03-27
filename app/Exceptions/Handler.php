<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // Little hack to use instanceof in switch: https://blog.liplex.de/switch-with-instanceof-in-php
        switch(true) {
            case $e instanceof NotFoundHttpException:
            case $e instanceof ModelNotFoundException:
                return response()->json([
                    'error' => [
                        'message'     => 'Not Found',
                        'status_code' => 404
                    ]
                ], 404);
                break;
            case $e instanceof MethodNotAllowedHttpException:
                return response()->json([
                    'error' => [
                        'message'     => 'Method Not Allowed',
                        'status_code' => 405
                    ]
                ], 405);
                break;
            default:
                return parent::render($request, $e);
        }
    }
}
