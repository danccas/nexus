<?php
namespace App\Http\Middleware;

use Core\Request;

class AuthenticateRest
{
   public function handle(Request $request, \Closure $next)
   {
    $auth = $request->header('Authorization');
    if(empty($auth)) {
        return response()->json(['success' => false]);
    }
    $auth = explode(' ', $auth);
    if(empty($auth[1])) {
        return response()->json(['success' => false]);
    }
    $auth = $auth[1];

    $token = db()->first("SELECT * FROM robusto.fn_api_access_token(:uuid)", ['uuid' => $auth]);
    if(empty($token)) {
        return response()->json(['success' => false]);
    }
    $request->user = $token;
    return $next($request);
   }
}