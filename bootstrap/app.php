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
        $middleware->alias(['role' => \App\Http\Middleware\EnsureRole::class]);
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if (! $request->expectsJson()) {
                $request->session()->flash('notice', 'Vui lòng đăng nhập tài khoản để tiếp tục đặt vé.');
                if (! $request->isMethod('GET') && ($suat = $request->route('suat'))) {
                    $request->session()->flash('_intended_suat_url', route('datve.chon-ghe', $suat));
                }
            }

            return route('login');
        });
        $middleware->redirectUsersTo(fn () => route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
