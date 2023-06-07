<?php

namespace App\Http\Controllers;

use Core\Controller;

class HelloWorldController extends Controller
{
    function index()
    {
		return view('helloworld.index');
    }
}
