<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanVoidInvoices
{
    /**
     * Handle an incoming request.
     * Verificar que el usuario autenticado tenga permisos para anular facturas
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No autenticado'
                ], 401);
            }
            return redirect()->route('login');
        }

        // Verificar que el usuario tenga permisos para anular facturas
        if (!Auth::user()->canVoidInvoices()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No tienes permisos para anular facturas. Solo administradores y propietarios pueden realizar esta acción.'
                ], 403);
            }

            return redirect()->back()->with('error',
                'No tienes permisos para anular facturas. Solo administradores y propietarios pueden realizar esta acción.'
            );
        }

        return $next($request);
    }
}
