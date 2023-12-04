<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use \App\Models\AuthModel;

class isLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next,$login_access)
    {
        if (empty(session('user_id'))) {            
            $username   = !empty($_COOKIE["userLog_1"]) ? base64_decode($_COOKIE["userLog_1"]) : "";
            $password   = !empty($_COOKIE["userLog_2"]) ? base64_decode($_COOKIE["userLog_2"]) : "";
            $auth_model     = new AuthModel();
            $auth_model->login($username,$password,'');
        }
        if ($login_access == 'true') {
            $login_access    = true;
        }else{
            $login_access    = false;            
        }
        $is_login   = !empty(session('user_id'));
        if ($login_access == $is_login) {
            return $next($request);
        }
        if ($is_login) {
            return redirect(route('dashboard'));
        }else{
            return redirect(route('login'));
        }
    }
}
