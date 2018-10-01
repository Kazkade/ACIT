<?php
use App\Part;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'DashboardController@index');

Route::get('ajax/part_info/{serial}', function($serial) {
  
  DB::enableQueryLog();
  
  $backstock = DB::table('locations')
    ->where('location_name', '=', 'Backstock')
    ->get();
  
  $part_info = DB::table('parts')
    ->join('inventories', function($join) use ($backstock)
    {
      $join->on('parts.id', '=', 'inventories.part_id')
           ->where('inventories.location_id', '=', $backstock[0]->id);
    })
    ->where('parts.part_serial','=', $serial)
    ->select(
            'parts.id', 'parts.part_name', 'parts.recommended_bagging', 'parts.part_cleaned',
            DB::raw('SUM(`inventories`.`to_total` - `inventories`.`from_total`) as "backstock"')
    )
    ->get();
  
  // Uncomment to see query.
  //dd(DB::getQueryLog());
  //die(json_encode($part_info));
  
  return response(json_encode($part_info), 200)
    ->header('Content-Type', 'text/json');
});


// Orders Routes
Route::post('orders/upload', 'OrderController@upload');
Route::get('deliver_order', 'OrderController@deliver');
Route::post('/orders/store', [
  "uses" => 'OrderController@store'
]);

Route::get('transfers/reverse/{id}', 'TransfersController@reverse');

// Bag Routes
Route::post('bags/mark/{id}', 'BagsController@mark');
Route::post('bags/unmark/{id}', 'BagsController@unmark');
Route::get('bags/destroy/{id}', 'BagsController@destroy');
Route::get('bags/unbag/{id}', 'BagsController@unbag');
Route::get('bags/', 'BagsController@index');

// Report Routes
Route::get('reports/print_list', 'ReportController@print_list');
Route::get('reports/delivery_report', 'ReportController@delivery_report');
Route::get('reports/filament_usage', 'ReportController@filament_usage');
Route::get('reports/weekly_scrap', 'ReportController@weekly_scrap');
Route::get('reports/engine/{query}', 'ReportController@report_engine');

// Delivery Routes
Route::get('deliveries/all', 'DeliveryController@all');

// Printer Routes
Route::post('printer/store', 'PrinterController@store');
Route::post('printer/toggle/{id}', 'PrinterController@toggle');
Route::post('printer/destroy/{id}', 'PrinterController@destroy');

// Machine Routes
Route::delete('machines/destroy/{id}', 'MachineController@destroy');

// Filament Routes
Route::post('filament/store', 'FilamentController@store');
Route::post('filament/toggle/{id}', 'FilamentController@toggle');
Route::post('filament/destroy/{id}', 'FilamentController@destroy');

// Part Routes
Route::get('parts/update_or_create/{json}', "PartsController@update_or_create");
Route::post('part/moratorium/{id}', 'PartsController@moratorium')->name('parts.moratorium');

// Overage Routes
Route::get('overages/resolve/{id}', "OverageController@resolve");
Route::get('overages/unresolve/{id}', "OverageController@unresolve");

// JSON Update Routes
Route::get('machines/ajaxupdate', "MachineController@ajaxupdate");
Route::get('users/json_update/{json}', "UserController@json_update");

// User Permission
Route::get('users/update_permission/{user_id}/{permission}/{value}', "UserController@update_permission");
Route::get('permissions', "PermissionController@index")->name('permissions.index');
Route::get('permissions/ajax', "PermissionController@ajax")->name('permissions.ajax');
Route::get('permissions/update_permission/{permission}/{value}/{description}', "PermissionController@update_permission");
Route::get('permissions/{permission}/{value}/{description}', "PermissionController@store")->name('permissions.store');
Route::get('permissions/destroy/{permission}',"PermissionController@destroy")->name('permissions.destroy');

// Invite Users
Route::get('users/invite/{email}', 'UserController@invite');

// Dev Tool Routes
Route::get('reset/inventory/{code}', 'DataController@reset_inventory');
Route::get('regenerate', 'DataController@regenerate');

// Resource Routes
Route::resource('deliveries', 'DeliveryController');
Route::resource('locations', 'LocationsController');
Route::resource('machines', 'MachineController');
Route::resource('maintenance', 'MaintenanceController');
Route::resource('task_schedule', 'TaskScheduleController');
Route::resource('orders', 'OrderController');
Route::resource('overages', 'OverageController');
Route::resource('parts', 'PartsController');
Route::resource('profiles', 'ProfileController');
Route::resource('transfers', 'TransfersController');
Route::resource('users', 'UserController');

// Error Routes
Route::get('unauthorized', function() {
  return View::make('errors.401');
})->name('unauthorized');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::auth();
});
