<?php

use Core\Route;

Route::get('', 'App\Http\Controllers\HelloWorldController@index');


/*
 * Example Library
 *
 *
 */
Route::get('library', 'App\Http\Controllers\LibraryController@index')->name('library.index');
#Route::post('library.tablefy', 'App\Http\Controllers\LibraryController@tablefy');
Route::post('library/repository', 'App\Http\Nexus\Views\LibraryTableView@response')->name('library.tablefy');
