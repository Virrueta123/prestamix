<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloquearUsuario
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
     
        // dd(Auth::check());
        // // **Comprobar si el usuario está bloqueado:**->status == "D"
         if (  Auth::user() && Auth::user()->status == "D" && $request->route()->getName() !== 'bloqueado') {
          
            //   **Redireccionar al usuario a una página de error o bloqueo:**
             return redirect()->route('bloqueado'); 
         }

        return $next($request);
    }
}
