<?php

namespace App\Http\Controllers;

use Core\Controller;

class LoginController extends Controller
{
    function index()
    {
		return view('welcome.index');
    }
}
