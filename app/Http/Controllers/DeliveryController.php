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
      DB::enableQueryLog();
      
      // Quantity Delivered
      $bags = DB::table('bags')
        ->where('delivered', '=', 0)
        ->get();
      
      // Get All Orders
      $orders = DB::table('orders')
        ->select(DB::raw('`updated_at`, MAX(`priority`) as "priority", `part_id`, SUM(`quantity`) as "ordered"'))
        ->groupBy('part_id')
        ->get();
      
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // The creation form is on the sidebar for admins.
        return view('pages.deliveries.index');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect("/deliveries");
    }
  
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
      $deliveries = DB::table('deliveries')
        ->join('users', 'users.id', '=', 'deliveries.user_id')
        ->join('bags', 'bags.delivery_id', '=', 'deliveries.id')
        ->select('deliveries.*', 'users.id', 'users.first_name', 'users.last_name', DB::raw('SUM(`bags`.`quantity`) as "total"'))
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
        ->join('overages', 'overages.part_id', '=', 'parts.id')
        ->select(
          'deliveries.*',
          'users.id',
          'users.first_name',
          'users.last_name',
          'bags.*',
          'parts.*',
          'overages.*'
        )
        ->where('bags.delivery_id', '=', $id)
        ->get();
      
      //print_r($report);
      //echo "<br>";
      //dd(DB::getQueryLog());
      
      return view('pages.deliveries.show')
        ->with('delivery', $delivery)
        ->with('report', $report);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('pages.deliveries.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return redirect("/deliveries")->with('success', 'Part Created!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
        return redirect()->route('deliveries.index')->with('success', 'Part '.$part->part_serial.' deleted. '.$deleted_inventories.' were deleted.');

    }
}

