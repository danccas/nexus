<?php
namespace App\Http\Middleware;

use Closure;

class Authenticate
{
   public function handle($request, Closure $next)
   {
      if(user()->is_valid()) {
         $labels = $request->route->getLabels();
         if(!empty($labels)) {
            if(user()->can($labels)) {
               return $next($request);
            } else {
               return redirect('/identificacion?deny=' . json_encode($labels));
            }
         } else {
            return $next($request);
         }
      } else {
         return redirect('/identificacion?no-valid');
      }
   }
}