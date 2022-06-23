<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParentsAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('parents')->check()) {
            return $next($request);
        } else {
            return redirect()->to(route('parents.login'));
        }
    }
}
