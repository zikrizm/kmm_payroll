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
    Route::resource('break-time', 'ShiftController', ['except' => ['update']]);
    Route::post('/break-time/{break_time}', 'ShiftController@update')->name('break_time.update');

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

    Route::resource('user', 'ManageUserController', ['except' => ['update']]);
    Route::resource('role', 'RoleController', ['except' => ['update']]);
    Route::resource('department', 'DepartmentController', ['except' => ['update']]);
    Route::resource('position', 'PositionController', ['except' => ['update']]);
    Route::resource('area', 'AreaController', ['except' => ['update']]);
    Route::resource('employee', 'EmployeeController', ['except' => ['update']]);
    Route::resource('kasbon', 'KasbonController', ['except' => ['update']]);
    Route::resource('resign', 'ResignController', ['except' => ['update']]);
    Route::resource('break-time', 'BreakTimeController', ['except' => ['update']]);
    Route::resource('timetable', 'TimetableController', ['except' => ['update']]);
    Route::resource('shift', 'ShiftController', ['except' => ['update']]);
    Route::resource('holiday', 'HolidayController', ['except' => ['update']]);
    Route::resource('device', 'DeviceController', ['except' => ['update']]);
    Route::resource('transaction', 'TransactionController', ['except' => ['update']]);

    Route::post('/user/{user}', 'ManageUserController@update')->name('user.update');
    Route::post('/role/{role}', 'RoleController@update')->name('role.update');
    Route::post('/department/{department}', 'DepartmentController@update')->name('department.update');
    Route::post('/position/{position}', 'PositionController@update')->name('position.update');
    Route::post('/area/{area}', 'AreaController@update')->name('area.update');
    Route::post('/employee/{employee}', 'EmployeeController@update')->name('employee.update');
    Route::post('/kasbon/{kasbon}', 'KasbonController@update')->name('kasbon.update');
    Route::post('/resign/{resign}', 'ResignController@update')->name('resign.update');
    Route::post('/break-time/{break_time}', 'BreakTimeController@update')->name('break-time.update');
    Route::post('/timetable/{timetable}', 'TimetableController@update')->name('timetable.update');
    Route::post('/shift/{shift}', 'ShiftController@update')->name('shift.update');
    Route::post('/holiday/{holiday}', 'HolidayController@update')->name('holiday.update');
    Route::post('/device/{device}', 'DeviceController@update')->name('device.update');
    Route::post('/transaction/{transaction}', 'TransactionController@update')->name('transaction.update');

    Route::get('/employee-photo', 'EmployeePhotoController@index')->name('employee-photo.index');
    Route::post('/employee-photo', 'EmployeePhotoController@store')->name('employee-photo.store');

    Route::get('/search-employee-for-dropdown', 'EmployeeController@searchEmployeeForDropdown')->name('employee.search-employee-for-dropdown');
    Route::get('/search-break-time-for-dropdown', 'BreakTimeController@searchBreakTimeForDropdown')->name('break-time.search-break-time-for-dropdown');
    Route::get('/search-timetable-for-dropdown', 'TimetableController@searchTimetableForDropdown')->name('timetable.search-timetable-for-dropdown');

    Route::post('/user-csv', 'ManageUserController@uploadUsers')->name('user.upload-csv');


    Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');
});
