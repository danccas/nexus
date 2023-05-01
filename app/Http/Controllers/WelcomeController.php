<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Lima');


Route::any('', function ($route) {

	return view('dashboard/welcome', compact('table'));
});

Route::else(function () {
	exit("Limpio2");
});
