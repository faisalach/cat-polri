<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class XssSanitizer
{
	public function handle(Request $request, Closure $next)
	{
		$input = $request->all();
		foreach($input as $key => $val){
			if ($key != 'questions') {
				if (is_array($val)) {
					array_walk_recursive($val, function(&$val,$key) {
						$val = strip_tags($val);
					});
				}else{
					$val = strip_tags($val);
				}
			}
			$input[$key] = $val;
		}
		$request->merge($input);
		return $next($request);
	}
}
