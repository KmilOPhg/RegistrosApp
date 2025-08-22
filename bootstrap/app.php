<?php

use App\Exceptions\AbonoMayorAlTotalException;
use App\Exceptions\AbonoNegativoException;
use App\Exceptions\AbonoNoEncontradoException;
use App\Helpers\JsonResponseHelper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /**
         * Registramos las excepciones
         * 1. Abono mayor
         * 2. Abono negativo
         * 3. Abono no encontrado
         */
        $exceptions->renderable(function (AbonoMayorAlTotalException $e) {
            return JsonResponseHelper::errorResponse(
                'No se puede abonar mas del total',
                ['Detalle' => $e->getMessage()],
                422);
        });

        $exceptions->renderable(function (AbonoNegativoException $e) {
            return JsonResponseHelper::errorResponse(
                'No se puede abonar un valor negativo',
                ['Detalle' => $e->getMessage()],
                422);
        });

        $exceptions->renderable(function (AbonoNoEncontradoException $e) {
            return JsonResponseHelper::errorResponse(
                'No se pudo encontrar el abono',
                ['Error' => $e->getMessage()],
                404);
        });

        //Excepcion general
        $exceptions->renderable(function (Exception $e) {
            return JsonResponseHelper::errorResponse(
                'Error en el servidor',
                ['Error' => $e->getMessage()],
            500);
        });
    })->create();
