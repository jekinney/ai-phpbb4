<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Allow if user is not authenticated and permission is for guests
        if (!auth()->check()) {
            if (in_array($permission, ['view_forums', 'view_topics', 'view_posts'])) {
                return $next($request);
            }
            return redirect()->route('login')->with('error', 'You must be logged in to perform this action.');
        }

        $user = auth()->user();

        // Check if user has the required permission
        if (!$user->can($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
