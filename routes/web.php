<?php

use Illuminate\Support\Facades\Auth;
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

Auth::routes();
Route::get('/business/register', 'BusinessController@getBusinessRegister')->name('business.index.register');
Route::post('/business/register', 'BusinessController@storeBusinessRegister')->name('business.store.register');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home.index');
    Route::get('/get-token-zkteco', 'HomeController@get_token_zkteco')->name('get_token_zkteco')->middleware('only.ajax');

    Route::resource('users', 'ManageUserController', ['except' => ['update']]);
    Route::post('/users/{user}', 'ManageUserController@update')->name('users.update');

    Route::resource('employees', 'EmployeeController', ['except' => ['update']]);
    Route::post('/employees/{employee}', 'EmployeeController@update')->name('employees.update');

    Route::resource('access-controls', 'AccessControlController', ['except' => ['update']]);
    Route::post('/access-controls/{access_control}', 'AccessControlController@update')->name('access-controls.update');

    Route::resource('work-sections', 'WorkSectionController', ['except' => ['update']]);
    Route::post('/work-sections/{work_section}', 'WorkSectionController@update')->name('work-sections.update');

    Route::resource('groups', 'GroupController', ['except' => ['update']]);
    Route::post('/groups/{group}', 'GroupController@update')->name('groups.update');

    Route::resource('shifts', 'ShiftController', ['except' => ['update']]);
    Route::post('/shifts/{shift}', 'ShiftController@update')->name('shifts.update');

    Route::resource('holidays', 'HolidayController', ['except' => ['update']]);
    Route::post('/holidays/{holiday}', 'HolidayController@update')->name('holidays.update');

    Route::get('/business/settings', 'BusinessController@getBusinessSettings')->name('business.index.settings');
    Route::post('/business/settings/{business}', 'BusinessController@updateBusinessSettings')->name('business.update.settings');

    Route::resource('/business/locations', 'BusinessLocationController', ['except' => ['update']]);
    Route::post('/business/locations/{location}', 'BusinessLocationController@update')->name('locations.update');

    // Route::get('/page', 'PageController@index')->name('page.index')->middleware('only.ajax');
    Route::get('/user-management', 'PageController@userManagement')->name('page.userManagement');
    Route::get('/company-profile', 'PageController@companyProfile')->name('page.companyProfile');

    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    // Route::middleware('role:admin')->get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

    Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');
});
