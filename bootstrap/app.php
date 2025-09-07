<?php

use Illuminate\Foundation\Application;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware): void {
        //
    })
    ->withExceptions(function ($exceptions): void {

        // Validation errors
        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Authorization errors
        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException|\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden: You do not have permission to perform this action.',
                ], 403);
            }
        });

        // Unauthenticated
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated: You must be logged in to perform this action.',
                ], 401);
            }
        });

        // Resource not found
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                ], 404);
            }
        });

        // Other HTTP exceptions
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'HTTP error',
                ], $e->getStatusCode());
            }
        });

        // Fallback for any other error
        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => app()->environment('local') ? $e->getMessage() : 'Something went wrong',
                ], 500);
            }
        });
    })
    ->create();
