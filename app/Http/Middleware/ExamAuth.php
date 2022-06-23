<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExamAuth
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
        if (auth('students')->check()) {
            return $next($request);
        } else {
            return redirect()->to(route('exams.show-login'));
        }
    }
}
