<?php

use App\Exceptions\AbonoMayorAlTotalException;
use App\Exceptions\AbonoNegativoException;
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
         */
        $exceptions->renderable(function (AbonoMayorAlTotalException $e) : JsonResponse {
            return response()->json([
                'message' => 'No se puede abonar mas del total',
                'errors' => ['Detalle' => $e->getMessage()],
            ], 422);
        });

        $exceptions->renderable(function (AbonoNegativoException $e) : JsonResponse {
            return response()->json([
                'message' => 'No se puede abonar un valor negativo',
                'errors' => ['Detalle' => $e->getMessage()],
            ], 422);
        });
    })->create();
