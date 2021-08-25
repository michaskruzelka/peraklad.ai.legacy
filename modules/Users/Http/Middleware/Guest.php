<?php

namespace Modules\Users\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Guest
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            $url = route(config('users.redirects.login'));
            if ( ! $request->ajax()) {
                return redirect($url);
            }
            return response()->json(['status' => 'fail', 'response' => $url], 401);
        }

        return $next($request);
    }
}
