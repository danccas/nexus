<?php
namespace App\Http\Middleware;

use Core\Request;

class AuthenticateRest
{
   public function handle(Request $request, \Closure $next)
   {
    return $next($request);
   }
}