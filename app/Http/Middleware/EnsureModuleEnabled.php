<?php

namespace App\Http\Middleware;

use App\Support\PlatformModules;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Explicit route middleware: module:tutoring or module:tutoring,crm_sales (ANY must be enabled).
 */
class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string ...$modules): Response
    {
        if ($modules === []) {
            return $next($request);
        }

        if (! PlatformModules::anyEnabled(...$modules)) {
            abort(404);
        }

        return $next($request);
    }
}
