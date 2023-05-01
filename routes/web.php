<?php

use Core\Route;

Route::get('', 'App\Http\Controllers\DashboardController@redirect')->middleware('auth');
Route::get('dashboard', 'App\Http\Controllers\DashboardController@index')->name('dashboard.index')->middleware('auth');
Route::get('dashboard/api/onpremise_top', 'App\Http\Controllers\DashboardController@api_onpremise_top');
Route::get('dashboard/api/onpremise', 'App\Http\Controllers\DashboardController@api_onpremise');
