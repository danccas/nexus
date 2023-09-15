<?php

namespace App\Http;


class Kernel
{
    public $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'authRest' => \App\Http\Middleware\AuthenticateRest::class,
    ];
    
    public $middlewareGroups = [
        'auth' => [
          \App\Http\Middleware\Authenticate::class,
        ]
      ];
}