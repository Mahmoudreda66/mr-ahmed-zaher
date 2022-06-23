<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;

class ActiveAdmin
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
        if(auth()->user()->active == 1){
            return $next($request);
        }else{
            auth()->logout();
            return redirect('/admin' . RouteServiceProvider::HOME)->with('error', 'لا يمكن لهذا الحساب الدخول للوحة التحكم نظراً لأنه تم إلغاء تفعيله من قبل المسئول.');
        }
    }
}
