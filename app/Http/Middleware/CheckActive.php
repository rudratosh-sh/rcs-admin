<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActive
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
        // if (auth()->check() && auth()->user()->status==0) {
        //     $message = 'Your account has been Inactive. Please contact administrator.';
        //     return redirect()->route('send-basic-sms-background')->withMessage($message);
        // }
        return $next($request);
    }

}
