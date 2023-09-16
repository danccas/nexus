<?php

namespace App\Http\Controllers;

use Core\Controller;

class LibraryController extends Controller
{
    public function index()
    {
		return view('library.index');
    }
    public function tablefy() {
        $rp = [];
        return response()->json($rp);
    }
}
