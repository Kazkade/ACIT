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

Route::get('dashboard', [
    'middleware' => 'auth:api',
    'uses' => 'PagesController@dashboard'
]);

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


// Extra Orders Routes
Route::post('orders/upload', 'OrderController@upload');
Route::get('deliver_order', 'OrderController@deliver');

// Additional Transfer Routes
Route::get('transfers/reverse/{id}', 'TransfersController@reverse');

// Bag Routes
Route::post('bags/mark/{id}', 'BagsController@mark');
Route::post('bags/unmark/{id}', 'BagsController@unmark');
Route::get('bags/dismantle/{id}', 'BagsController@dismantle');
Route::get('bags/', 'BagsController@index');

// Report Routes
Route::get('reports/print_list', 'ReportController@print_list');
Route::get('reports/delivery_report', 'ReportController@delivery_report');
Route::get('reports/filament_usage', 'ReportController@filament_usage');
Route::get('reports/weekly_scrap', 'ReportController@weekly_scrap');
Route::get('reports/engine/{query}', 'ReportController@report_engine');
Route::get('deliveries/all', 'DeliveryController@all');

// Printer Routes
Route::post('printer/store', 'PrinterController@store');
Route::post('printer/toggle/{id}', 'PrinterController@toggle');
Route::post('printer/destroy/{id}', 'PrinterController@destroy');

// Filament Routes
Route::post('filament/store', 'FilamentController@store');
Route::post('filament/toggle/{id}', 'FilamentController@toggle');
Route::post('filament/destroy/{id}', 'FilamentController@destroy');

// Additional Part Routes
Route::get('/parts/update_or_create/{json}', "PartsController@update_or_create");
Route::post('part/moratorium/{id}', 'PartsController@moratorium')->name('parts.moratorium');

// Resets
Route::get('/reset/inventory/{code}', 'DataController@reset_inventory');

// Resource Routes
Route::resource('parts', 'PartsController');
Route::resource('locations', 'LocationsController');
Route::resource('transfers', 'TransfersController');
Route::resource('orders', 'OrderController');
Route::resource('deliveries', 'DeliveryController');
Route::resource('users', 'UserController');
Route::resource('profiles', 'ProfileController');
Route::resource('overages', 'OverageController');
Route::resource('configuration', 'ConfigurationController');
Route::resource('users', 'UserController');
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
