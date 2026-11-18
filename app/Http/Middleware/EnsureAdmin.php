<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * 管理者以外は、一般ユーザーの打刻画面へ戻す
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->admin_status) {
            return redirect('/attendance');
        }

        return $next($request);
    }
}