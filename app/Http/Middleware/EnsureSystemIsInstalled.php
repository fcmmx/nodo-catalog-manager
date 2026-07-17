<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSystemIsInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('testing')) {
            return $next($request);
        }

        $installed = file_exists(storage_path('app/installed.lock'));

        if (! $installed && ! $request->is('install*') && ! $request->is('up')) {
            return redirect('/install');
        }

        if ($installed && $request->is('install*')) {
            abort(404);
        }

        return $next($request);
    }
}
