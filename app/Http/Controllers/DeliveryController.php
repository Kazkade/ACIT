<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Part;
use App\Transfer;
use App\Location;
use App\Inventory;
use App\Bag;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
      if(!\App\PermissionEnforcer::Protect("deliveries_index")) { return response("Unauthorized", 401); }
      DB::enableQueryLog();
      
      // Quantity Delivered
      $bags = DB::table('bags')
        ->where('delivered', '=', 0)
        ->get();
      
      // Get All Orders
      $orders = DB::table('orders')
        ->select(DB::raw('`created_at`, MAX(`priority`) as "priority", `part_id`, SUM(`quantity`) as "ordered"'))
        ->where(DB::raw('`quantity` - `filled`'), '>', '0')
        ->groupBy('part_id')
        ->get();
      
      $bags1 = DB::table('bags')
        ->join('parts', 'parts.id', '=', 'bags.part_id')
        ->join('users', 'users.id', '=', 'bags.created_by')
        ->select('bags.*', 'parts.part_serial', 'parts.part_name','users.id', 'users.last_name', 'users.first_name')
        ->where('bags.delivered', '=', 0)
        ->orderBy('parts.part_serial')
        ->get();
      
      //print('<pre>'.print_r($bags1, true).'</pre>');
      //die();
      
      foreach($orders as $order)
      {
        $order->bag_count = 0;
        foreach($bags as $bag)
        {
          if($bag->part_id == $order->part_id)
          {
            $order->bag_count++;
          }
        }
      }
      
      $users = DB::table('users')
        ->select(DB::raw('`id`, `first_name`, `last_name`'))
        ->get();
      
      $parts = Part::all();
      
      // Testing (Uncomment when using.)
      //dd(DB::getQueryLog());
      
      // Return View
      return view('pages.deliveries.index')
        ->with('bags', $bags)
        ->with('orders', $orders)
        ->with('users', $users)
        ->with('parts', $parts);
    }
  
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
      if(!\App\PermissionEnforcer::Protect("deliveries_index")) { return response("Unauthorized", 401); }
      $deliveries = DB::table('deliveries')
        ->join('users', 'users.id', '=', 'deliveries.user_id')
        ->join('bags', 'bags.delivery_id', '=', 'deliveries.id')
        ->join('overages', 'overages.delivery_id', '=', 'deliveries.id')
        ->select(
          'deliveries.*', 
          'users.id as "user_id', 
          'users.first_name', 
          'users.last_name', 
          'overages.quantity',
          'overages.resolved',
          DB::raw('SUM(`bags`.`quantity`) as "total"'))
        ->groupBy('deliveries.id')
        ->get();
      
      return view('pages.deliveries.all')
        ->with('deliveries', $deliveries);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      if(!\App\PermissionEnforcer::Protect("deliveries_index")) { return response("Unauthorized", 401); }
      DB::enableQueryLog();
      
      $delivery = DB::table('deliveries')
        ->join('users', 'users.id', '=', 'deliveries.user_id')
        ->select('deliveries.*', DB::raw('date(deliveries.updated_at) as "date"'), 'users.id', 'users.first_name', 'users.last_name')
        ->where('deliveries.id', '=', $id)
        ->first();
      
      $report = DB::table('bags')
        ->join('deliveries', 'deliveries.id', '=',  'bags.delivery_id')
        ->join('users', 'users.id', '=', 'bags.created_by')
        ->join('parts', 'bags.part_id', '=', 'parts.id')
        ->select(
          'deliveries.*',
          'users.id as "user_id"',
          'users.first_name',
          'users.last_name',
          'bags.*',
          DB::raw('SUM(`bags`.`quantity`) as "quantity"'),
          'parts.*'
        )
        ->groupBy('bags.part_id')
        ->where('bags.delivery_id', '=', $id)
        ->get();
      
      $overages = DB::table('overages')
        ->where('delivery_id', '=', $id)
        ->get();
      
      //print_r($report);
      //echo "<br>";
      //dd(DB::getQueryLog());
      
      return view('pages.deliveries.show')
        ->with('delivery', $delivery)
        ->with('report', $report);
    }
}

