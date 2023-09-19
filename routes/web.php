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


Route::get('financiero', 'App\Http\Controllers\FinancieroController@index');
Route::post('financiero/movimientos_tablefy', 'App\Http\Controllers\FinancieroController@movimientos_tablefy')->name('financiero.movimientos_tablefy');
Route::post('financiero/movimientos_pendientes_tablefy', 'App\Http\Controllers\FinancieroController@movimientos_pendientes_tablefy')->name('financiero.movimientos_pendientes_tablefy');

Route::post('financiero/cuentas_debito_tablefy', 'App\Http\Controllers\FinancieroController@cuentas_debito_tablefy')->name('financiero.cuentas_debito_tablefy');
Route::post('financiero/cuentas_credito_tablefy', 'App\Http\Controllers\FinancieroController@cuentas_credito_tablefy')->name('financiero.cuentas_credito_tablefy');

Route::get('financiero/create/movimiento', 'App\Http\Controllers\FinancieroController@create_movimiento')->name('financiero.create_movimiento');

Route::post('financiero/repository/cuentas_debito', 'App\Http\Nexus\Views\CuentasDebitoTableView@response')->name('repository.cuentas_debito');
