<?php

use Core\Route;

#Route::post('identificacion', 'App\Http\Controllers\Auth\LoginController@login_check');
Route::any('identificacion', 'App\Http\Controllers\Auth\LoginController@login')->name('login');
Route::get('registrarse', 'App\Http\Controllers\Auth\LoginController@register');
Route::get('salir', 'App\Http\Controllers\Auth\LoginController@logout');
