<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Restreint l'accès aux rôles passés en paramètre.
     * Usage : ->middleware('role:admin,surveillant,caissier')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles, true)) {
            abort(403, "Accès réservé. Votre profil ne permet pas cette action.");
        }

        return $next($request);
    }
}
