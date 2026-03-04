<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            // For AJAX requests, return a 419 JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'انتهت الجلسة بسبب عدم النشاط، الرجاء تسجيل الدخول مرة أخرى',
                    'reload' => true
                ], 419);
            }

            // For regular requests, redirect to login with original input and a friendly message
            return redirect()->route('login')
                ->with('warning', 'انتهت الجلسة بسبب عدم النشاط، الرجاء تسجيل الدخول مرة أخرى')
                ->withInput($request->except(['_token', 'password', 'password_confirmation', 'files']));
        });
    })->create();
