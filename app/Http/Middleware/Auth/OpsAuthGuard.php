<?php
namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;

class OpsAuthGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        config(['auth.defaults.guard' => 'ops']);

        return $next($request);
    }
}
