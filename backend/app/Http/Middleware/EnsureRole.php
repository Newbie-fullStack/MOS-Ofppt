<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (! $user) {
            return new JsonResponse(['message' => 'Non authentifie.'], 401);
        }

        $allowed = array_map('strtoupper', $roles);
        $currentRole = strtoupper((string) ($user->role->value ?? $user->role));

        if (! in_array($currentRole, $allowed, true)) {
            return new JsonResponse([
                'message' => 'Acces refuse. Droits insuffisants.',
                'error' => 'INSUFFICIENT_PERMISSIONS',
            ], 403);
        }

        return $next($request);
    }
}
