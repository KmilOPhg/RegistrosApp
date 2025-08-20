<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Una lista de los tipos de excepción que no deberían reportarse.
     *
     * @var array
     */
    protected $dontReport = [
        NotFoundHttpException::class,
        ValidationException::class,
    ];

    /**
     * Una lista de los inputs que nunca son pasados a la sesión
     * en errores de validación.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registra los callbacks de manejo de excepciones para la aplicación.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
