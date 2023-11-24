<?php

use App\Events\HelloEvent;
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

Auth::routes();
// * Home
Route::get('/', 'HomeController@index')->name('home.index');
// * Businness register
Route::get('/business/register', 'BusinessController@getBusinessRegister')->name('business.index.register');
Route::post('/business/register', 'BusinessController@storeBusinessRegister')->name('business.store.register');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home.index');
    Route::get('/get-token-zkteco', 'HomeController@get_token_zkteco')->name('get_token_zkteco')->middleware('only.ajax');
    Route::get('/upload_csv', 'HelperController@upload_csv')->name('upload_csv');

    // * Businness setting
    Route::get('/business/setting', 'BusinessController@getBusinessSettings')->name('business.index.settings');
    Route::post('/business/setting/{business}', 'BusinessController@updateBusinessSettings')->name('business.update.settings');
    // * Businness location
    Route::resource('/business/location', 'BusinessLocationController', ['except' => ['update']]);
    Route::post('/business/location/{location}', 'BusinessLocationController@update')->name('locations.update');

    // * user
    Route::resource('user', 'ManageUserController', ['except' => ['update']]);
    Route::post('/user/{user}', 'ManageUserController@update')->name('user.update');
    // * role
    Route::resource('role', 'RoleController', ['except' => ['update']]);
    Route::post('/role/{role}', 'RoleController@update')->name('role.update');
    // * activity log
    Route::get('/activity-log', 'ActifityLogController@index')->name('activity-log.index');

    // * department
    Route::resource('department', 'DepartmentController', ['except' => ['update']]);
    Route::post('/department/{department}', 'DepartmentController@update')->name('department.update');
    // * position
    Route::resource('position', 'PositionController', ['except' => ['update']]);
    Route::post('/position/{position}', 'PositionController@update')->name('position.update');
    // * area
    Route::resource('area', 'AreaController', ['except' => ['update']]);
    Route::post('/area/{area}', 'AreaController@update')->name('area.update');
    // * device
    Route::resource('device', 'DeviceController', ['except' => ['update']]);
    Route::post('/device/{device}', 'DeviceController@update')->name('device.update');

    // * employee
    Route::resource('employee', 'EmployeeController', ['except' => ['update']]);
    Route::post('/employee/{employee}', 'EmployeeController@update')->name('employee.update');
    Route::get('/employee-import-template', 'EmployeeController@employeeImportTemplate')->name('employee.employeeImportTemplate');
    Route::get('/employee-export', 'EmployeeController@employeeExport')->name('employee.employeeExport');
    Route::get('/employee-csv', 'EmployeeController@uploadCSV')->name('employee.uploadCSV');
    Route::post('/employee-csv', 'EmployeeController@uploadCSV_store')->name('employee.uploadCSV-store');
    // * employee photo
    Route::get('/employee-photo', 'EmployeePhotoController@index')->name('employee-photo.index');
    Route::get('/employee-detail/{employee}', 'EmployeePhotoController@detail')->name('employee-photo.detail');
    Route::post('/employee-photo', 'EmployeePhotoController@store')->name('employee-photo.store');
    // * kasbon
    Route::resource('kasbon', 'KasbonController', ['except' => ['update']]);
    Route::post('/kasbon/{kasbon}', 'KasbonController@update')->name('kasbon.update');
    // * transaction
    Route::resource('transaction', 'TransactionController', ['except' => ['update', 'edit']]);
    // * resign
    Route::resource('resign', 'ResignController', ['except' => ['update']]);
    Route::post('/resign/{resign}', 'ResignController@update')->name('resign.update');

    // * break-time
    Route::resource('break-time', 'BreakTimeController', ['except' => ['update']]);
    Route::post('/break-time/{break_time}', 'BreakTimeController@update')->name('break-time.update');
    // * timetable
    Route::resource('timetable', 'TimetableController', ['except' => ['update']]);
    Route::post('/timetable/{timetable}', 'TimetableController@update')->name('timetable.update');
    // * shift
    Route::resource('shift', 'ShiftController', ['except' => ['update']]);
    Route::post('/shift/{shift}', 'ShiftController@update')->name('shift.update');
    // * holiday
    Route::resource('holiday', 'HolidayController', ['except' => ['update']]);
    Route::post('/holiday/{holiday}', 'HolidayController@update')->name('holiday.update');

    // * operational.
    Route::resource('operational', 'OperationalController', ['except' => ['update']]);
    Route::post('/operational/{operational}', 'OperationalController@update')->name('operational.update');
    Route::get('/get-operational-timetable-card', 'OperationalController@get_operational_timetable_card')->name('operasional.get-operational-timetable-card');

    // * request-help.
    Route::resource('request-help', 'RequestHelpController', ['except' => ['update']]);
    Route::post('/request-help/{request_help}', 'RequestHelpController@update')->name('request-help.update');
    // * request-task.
    Route::resource('request-task', 'RequestTaskController', ['except' => ['update']]);
    Route::post('/request-task/{request_task}', 'RequestTaskController@update')->name('request-task.update');
    Route::get('/get-employee-position', 'RequestTaskController@get_employee_position')->name('request-task.get_employee_position');

    // * salary-archive
    Route::get('/salary-archive', 'SalaryArchiveController@index')->name('salary-archive.index');
    Route::get('/salary-archive/re-calculate', 'SalaryArchiveController@get_re_calculate')->name('salary-archive.get-re-calculate');
    Route::post('/salary-archive/re-calculate', 'SalaryArchiveController@save_re_calculate')->name('salary-archive.save-re-calculate');
    Route::get('/salary-archive/{id}', 'SalaryArchiveController@show')->name('salary-archive.show');
    // * attendance-report
Route::resource('attendance-report', 'AttendanceReportController', ['except' => ['update', 'show']]);
    // * attendance-card
    Route::resource('attendance-card', 'AttendanceReportCardController', ['except' => ['update', 'show']]);
    // * attendance-operational
    Route::resource('attendance-operational', 'AttenOpReportController', ['except' => ['update', 'show', 'edit']]);
    // * payroll-report
    Route::resource('payroll-report', 'PayrollReportController', ['except' => ['update', 'show', 'delete']]);
    // * food-archive
    Route::get('/food-archive', 'FoodArchiveController@index')->name('food-archive.index');
    Route::get('/food-archive/{id}', 'FoodArchiveController@show')->name('food-archive.show');

    // Route::resource('additional-employee', 'AdditionalEmployeeController', ['except' => ['update']]);
    // Route::post('/additional-employee/{additional_employee}', 'AdditionalEmployeeController@update')->name('additional-employee.update');
    // Route::resource('rendaman', 'RendamanController', ['except' => ['update']]);
    // Route::post('/rendaman/{rendaman}', 'RendamanController@update')->name('rendaman.update');

    // Route::get('/attendance-report/attendance-card', 'AttendanceReportController@showAttendanceCard')->name('show-attendance-card');
    // Route::post('/attendance-report/attendance-card', 'AttendanceReportController@checkAttendanceCard')->name('check-attendance-card');
    // Route::get('/operational/{operational}/add-employees-to-help', 'OperationalController@add_employees_to_help')->name('operasional.add-employees-to-help');
    // Route::post('/operational/add-employees-to-help', 'OperationalController@post_employees_to_help')->name('operasional.add-employees-to-help.store');





    Route::get('/search-employee-off-in-depts', 'EmployeeController@employee_off_in_depts')->name('employee.search-employee-off-in-depts');
    Route::get('/search-employee-request-position', 'EmployeeController@employee_request_position')->name('employee.search-employee-request-position');
    Route::get('/search-employee-for-dropdown', 'EmployeeController@searchEmployeeForDropdown')->name('employee.search-employee-for-dropdown');
    Route::get('/search-break-time-for-dropdown', 'BreakTimeController@searchBreakTimeForDropdown')->name('break-time.search-break-time-for-dropdown');
    Route::get('/search-timetable-for-dropdown', 'TimetableController@searchTimetableForDropdown')->name('timetable.search-timetable-for-dropdown');

    Route::post('/user-csv', 'ManageUserController@uploadUsers')->name('user.upload-csv');




    // * PRINT
    Route::prefix('/print')->group(function () {
        Route::get('/payroll-report', 'PrintReportContoller@print_payroll_report')->name('print.payroll_report');
        Route::get('/card-report', 'PrintReportContoller@print_card_report')->name('print.card_report');
        Route::get('/card-attendance', 'PrintReportContoller@print_card_attendance')->name('print.card_attendance');
        Route::get('/card-attendance-operational', 'PrintReportContoller@print_card_attendance_operational')->name('print.card_attendance');
    });

    // * TSO.
    Route::prefix('/TSO')->group(function () {
        Route::get('/', 'TSOController@index')->name('TSO.index');
        Route::post('/approve', 'TSOController@store_approve_tso')->name('TSO.store.approve');
        Route::post('/cancel-approve/{id}', 'TSOController@destroy_approve_tso')->name('TSO.destroy.approve');
        
        Route::get('/set-status-lb', 'TSOController@set_status_lb')->name('TSO.set-starus-lb');
        Route::post('/set-status-lb', 'TSOController@save_set_status_lb')->name('TSO.save-set-starus-lb');
    });

    Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');
});
