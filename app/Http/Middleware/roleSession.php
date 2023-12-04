<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class roleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next,...$roles)
    {
        $user_group_id   = session('user_group_id');
        if (in_array($user_group_id,$roles)) {
            return $next($request);
        }else{
            if ($user_group_id == 3) {
                return redirect(route('user.dashboard'));
            }else{
                return redirect(route('dashboard'));
            }
        }
    }
}
