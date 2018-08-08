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
      
      // Order Location ID
      $order_location = DB::table('locations')
        ->where('location_name', '=', 'Orders')
        ->get();
      $order_location = $order_location[0]->id;
      
      // Quantity Delivered
      $bags = DB::table('bags')
        ->where('delivered', '=', 0)
        ->get();
      
      // Get All Orders
      $orders = DB::table('orders')
        ->select(DB::raw('`updated_at`, MAX(`priority`) as "priority", `part_id`, SUM(`quantity`) as "ordered"'))
        ->groupBy('part_id')
        ->get();
      
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
        return redirect("/deliveries")->with('success', 'Part '.$part->part_serial.' Created! '.$deleted_inventories.' were deleted.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      
        return view('pages.deliveries.show');
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

