<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                return redirect(match($user->role) {
                    'teacher'          => route('teacher.dashboard'),
                    'parent'           => route('parent.dashboard'),
                    'admin'            => route('admin.dashboard'),
                    'senior_assistant' => route('senior.dashboard'),
                    default            => '/',
                });
            }
        }

        return $next($request);
    }
}
