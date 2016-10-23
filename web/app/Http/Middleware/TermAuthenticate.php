<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\User;

class TermAuthenticate extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
		$next = parent::handle($request, $next, $guard);
		if (!User::validRole($request, Auth::guard($guard)->user())) {
			if ($request->ajax() || $request->wantsJson()) {
				return response('Unauthorized.', 401);
			} else {
				return redirect()->guest('login');
			}
		}
		return $next;
    }
}
