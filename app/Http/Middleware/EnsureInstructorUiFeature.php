<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks instructor routes when the matching instructor_ui flag is off.
 */
class EnsureInstructorUiFeature
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $key = match ($feature) {
            'tutoring' => 'show_tutoring',
            'live', 'live_broadcast' => 'show_live_broadcast',
            'courses' => 'show_courses',
            'libraries' => 'show_libraries',
            'consultations' => 'show_consultations',
            'learning_paths' => 'show_learning_paths',
            default => $feature,
        };

        if (! instructor_ui($key, true)) {
            abort(404);
        }

        return $next($request);
    }
}
