<?php
use App\Part;
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
  
  return response(json_encode($part_info), 200)
    ->header('Content-Type', 'text/json');
});

//Route::get('/dashboard', 'PagesController@dashboard');
Route::resource('parts', 'PartsController');
Route::resource('locations', 'LocationsController');
Route::resource('transfers', 'TransfersController');
Route::resource('orders', 'OrderController');
Route::resource('deliveries', 'DeliveryController');

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
  
    Route::get('/home', 'HomeController@index');
});
