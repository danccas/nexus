<?php

namespace App\Http\Controllers;

use Core\Controller;

class WelcomeController extends Controller
{
    function index()
    {
		return view('welcome.index');
    }
}
