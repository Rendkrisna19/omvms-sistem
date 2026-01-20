<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Pastikan baris ini ada (otomatis dari install:api)
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Di sini kita mendaftarkan Middleware khusus API jika ada
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Alias Middleware (agar bisa dipanggil di route misal: 'role:superadmin')
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class, // Kita akan buat ini nanti
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom error response agar selalu JSON (Opsional tapi bagus untuk API)
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });
    })
    ->create();