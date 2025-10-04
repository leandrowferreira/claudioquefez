<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDrawPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se a senha já foi validada na sessão, permite acesso
        if (session('draw_authenticated')) {
            return $next($request);
        }

        // Redireciona para o formulário de senha
        return redirect()->route('draw.password');
    }
}
