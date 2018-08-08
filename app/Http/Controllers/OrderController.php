<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Part;
use App\Transfer;
use App\Location;
use App\Inventory;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class OrderController extends Controller
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
      
  
      
      //$parts = Part::all();
      $parts = Part::orderBy('part_serial', 'asc')->paginate(1000);
      // $posts = DB::select('SELECT * FROM parts');
      return view('pages.parts.index')->with('parts', $parts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // The creation form is on the sidebar for admins.
        return view('pages.orders.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      
        return view('pages.orders.show')
          ->with('inventories', $inventories)
          ->with('locations', $locations)
          ->with('part', $part);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('pages.orders.edit')->with('part', $part);
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
        //
        return redirect("/orders")->with('success', 'Part Created!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
        return redirect()->route('orders.index')->with('success', 'Part '.$part->part_serial.' deleted. '.$deleted_inventories.' were deleted.');

    }
}

