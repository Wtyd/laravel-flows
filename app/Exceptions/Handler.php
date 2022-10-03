<?php

namespace App\Exceptions;

use Src\Utils\Foundation\Http\ApiResponse;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
        if ($exception instanceof ModelNotFoundException) {
            return ApiResponse::json(null, ApiResponse::ESTADO_404_RECURSO_NO_ENCONTRADO, [['mensaje' => 'El recurso no ha sido encontrado']]);
        }

        // Excepciones de Dominio
        if ('Src' === explode('\\', get_class($exception))[0]) {
            if (0 !== $exception->getCode()) {
                return ApiResponse::json(null, $exception->getCode(), [['mensaje' => $exception->getMessage()]]);
            } else {
                // Por defecto, las excepciones de domino devuelven 400.
                return ApiResponse::json(null, ApiResponse::ESTADO_400_ERROR, [['mensaje' => $exception->getMessage()]]);
            }
        }

        // Excepción que no sea de autentificación
        if (!is_a($exception, AuthenticationException::class)) {
            return ApiResponse::json(null, 500, [['mensaje' => $exception->getMessage()]]);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
                    ? response()->json(['message' => $exception->getMessage()], 401)
                    : redirect()->guest(url('/login'));
    }

    /**
     * Register the exception handling callbacks for the application.
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
