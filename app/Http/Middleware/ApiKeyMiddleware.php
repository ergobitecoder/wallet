<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $key = $request->header('api_key');
        
        if ( $key !== config('app.api_key') ) {
            $error_code = "400";
            $success = false;
            $response = ["error" => array("code" => $error_code, "message" => "InValid API Key"), "success" => $success];

            return response()->json($response, $error_code);
        }

        return $next($request);
    }
}
