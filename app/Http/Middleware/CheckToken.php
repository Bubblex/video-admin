<?php

namespace App\Http\Middleware;

use Closure;

use App\Library\Util;
use App\Models\User;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->token || !User::where('token', $request->token)->first()) {
            return Util::responseData(100, '登录状态已过期，请重新登录');
        }
        return $next($request);
    }
}
