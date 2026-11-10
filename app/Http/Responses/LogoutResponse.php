<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    /**
     * ログアウト後の移動先を決める
     */
    public function toResponse($request)
    {
        if ($request->is('admin/*')) {
            return redirect('/admin/login');
        }

        return redirect('/login');
    }
}