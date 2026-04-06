<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('guest_id')) {
            return redirect()->route('guest.login')
                ->with('error', 'Veuillez vous connecter pour accéder à votre espace.');
        }

        return $next($request);
    }
}
