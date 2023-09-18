<?php

namespace App\Http\Controllers;

use Core\Controller;

class LibraryController extends Controller
{
    public function index()
    {
  		return view('library.index');
    }
}
