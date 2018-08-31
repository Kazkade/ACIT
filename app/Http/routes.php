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

Route::get('/', 'PagesController@index');

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

// Send JSON to PartsController
Route::post('/parts/update_or_create', "PartsController@update_or_create");

// Upload New Orders
Route::post('orders/upload', 'OrderController@upload');

// Bag Routes
Route::post('bags/mark/{id}', 'BagsController@mark');
Route::post('bags/unmark/{id}', 'BagsController@unmark');

// Report Routes
Route::get('reports/print_list', 'ReportController@print_list');
Route::get('reports/delivery_report', 'ReportController@delivery_report');
Route::get('reports/filament_usage', 'ReportController@filament_usage');
Route::get('reports/weekly_scrap', 'ReportController@weekly_scrap');
Route::get('reports/engine/{query}', 'ReportController@report_engine');

// Printer Routes
Route::post('printer/store', 'PrinterController@store');
Route::post('printer/toggle/{id}', 'PrinterController@toggle');
Route::post('printer/destroy/{id}', 'PrinterController@destroy');

// Additional Part Routes
Route::post('part/moratorium/{id}', 'PartsController@moratorium')->name('parts.moratorium');

// Resource Routes
Route::resource('parts', 'PartsController');
Route::resource('locations', 'LocationsController');
Route::resource('transfers', 'TransfersController');
Route::resource('orders', 'OrderController');
Route::resource('deliveries', 'DeliveryController');
Route::resource('users', 'UserController');
Route::resource('profiles', 'ProfileController');
Route::resource('configuration', 'ConfigurationController');
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
