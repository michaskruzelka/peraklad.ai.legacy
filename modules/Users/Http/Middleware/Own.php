<?php

namespace Modules\Users\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;
use Modules\Users\Entities\User;

class Own
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ( ! $request->route('user')->isYou()) {
            if ( ! $request->ajax()) {
                return abort(403);
            }
            return response()->json('Not permitted', 403);
        }
        return $next($request);
    }

    /**
     * @param Request $request
     * @param $response
     */
    public function terminate(Request $request, $response)
    {

    }
}
