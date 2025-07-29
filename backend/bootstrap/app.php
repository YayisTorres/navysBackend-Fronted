<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware global para API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Registrar middleware personalizado
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Middleware para CORS si es necesario
        $middleware->web(append: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejo de excepciones personalizado si es necesario
        $exceptions->render(function (Throwable $e) {
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }

            if ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado'
                ], 403);
            }
        });
    })
    ->create();
