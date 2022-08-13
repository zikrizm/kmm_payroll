<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user-management', 'UserManagementController@index')->name('userManagement.index');
Route::post('/user-management', 'UserManagementController@store')->name('userManagement.store');
Route::get('/user-management/show', 'UserManagementController@show')->name('userManagement.show');
Route::get('/user-management/getComponent', 'UserManagementController@getComponent')->name('userManagement.getComponent');
Route::post('/user-management/update', 'UserManagementController@update')->name('userManagement.update');
Route::post('/user-management/delete', 'UserManagementController@destroy')->name('userManagement.destroy');
// Route::get('/nota/create', 'NotaController@create')->name('nota.create');
// Route::get('/nota/cetak', 'NotaController@cetak')->name('nota.cetak');
// Route::post('/nota', 'NotaController@store')->name('nota.store');
// Route::get('/nota/search', 'NotaController@search')->name('nota.search');
// Route::post('/nota/delete/{nota}', 'NotaController@destroy')->name('nota.delete');
// Route::get('/nota/{nota}', 'NotaController@edit')->name('nota.edit');
// Route::post('/nota/{nota}', 'NotaController@update')->name('nota.update');