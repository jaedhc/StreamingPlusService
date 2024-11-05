<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards){
        if($token = $request->cookie('cookie_token')){
            $request->headers-set('Authorization', 'Bearer'.$token);
        }

        if (!Auth::guard($guard)->check()) {
            // Devuelve un JSON de error en lugar de redirigir
            return Response::json(['message' => 'Unauthorized'], 401);
        } else {
            $this->authenticate($request, $guards);
            return $next;
        }
    }
}
