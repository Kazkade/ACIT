<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Part;
use App\Transfer;
use App\Location;
use App\Inventory;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class PartsController extends Controller
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
      
      
      /*
      // Comment this out to avoid duplicate inventories.
      // This will, when run, create inventories for every part and every locations.
      // You should only need this when you've truncated both the inventories, parts, and transfers tables
      // and are re-uploading the CSV for the parts list.
      
      $parts = Part::all();
      $locations = Location::all();
      foreach($parts as $part)
      {
        foreach($locations as $location)
        {
          $inventory = new Inventory;
          $inventory->location_id = $location->id;
          $inventory->part_id = $part->id;
          $inventory->to_total = 0;
          $inventory->from_total = 0;
          $inventory->save();
        }
          
      }
      */
      //SELECT `part_id`, SUM(`to_total`+`from_total`) as "Inventory" FROM `inventories` GROUP BY `part_id`
      $parts = Part::orderBy('part_serial', 'asc')->paginate(100);
      
      $backstock_location_id = DB::table('locations')
        ->where('location_name', '=', 'Backstock')
        ->get();
      
      $backstock_location_id = $backstock_location_id[0]->id;
      
      $inventories = DB::table('inventories')
        ->select(DB::raw('part_id, SUM(`to_total`+`from_total`) as "inventory"'))
        ->where('location_id', '=', $backstock_location_id)
        ->groupBy('part_id')
        ->get();
      
      $bags = DB::table('bags')
        ->select(DB::raw('part_id, SUM(`quantity`) as "quantity"'))
        ->where('delivered', '=', 0)
        ->groupBy('part_id')
        ->get();
      
      foreach($parts as $part)
      {
        foreach($inventories as $inventory)
        {
          if($inventory->part_id == $part->id)
          {
            $part->inventory = $inventory->inventory;
          }
        }
        foreach($bags as $bag)
        {
          if($bag->part_id == $part->id)
          {
            $part->inventory += $bag->quantity;
          }
        }
      }
      // $posts = DB::select('SELECT * FROM parts');
      return view('pages.parts.index')
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
        return view('pages.parts.index');
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
        $this->validate($request, [
          'part_name' => 'required',
          'part_serial' => 'required',
          'part_color' => 'required',
          'part_version' => 'required',
          'part_weight' => 'required',
        ]);
      
        // Create Part
        $part = new Part;
        $part->part_name = $request->input('part_name');
        $part->part_serial = $request->input('part_serial');
        $part->part_color = $request->input('part_color');
        $part->part_weight = $request->input('part_weight');
        $part->part_version = $request->input('part_version');
        if($request->get('part_cleaned') == null) {
          $part->part_cleaned = 0;
        } else {
          $part->part_cleaned = 1;
        }
        $part->part_quantity = 0;
        $part->save();
        // Create Inventories for part.
        $part = Part::orderBy('id')->first;
        $locations = Location::all();
        foreach($locations as $location)
        {
          $inventory = new Inventory;
          $inventory->part_id = $part->id;
          $inventory->location_id = $location->id;
          $inventory->to_total = 0;
          $inventory->from_total = 0;
          $inventory->save();
        }
        
        return redirect("/parts")->with('success', 'Part '.$part->part_serial.' Created! '.$deleted_inventories.' were deleted.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $part = Part::find($id);
        $locations = Location::all();
        $inventories = DB::table('inventories')->where('part_id', '=', $id)->get();
        
        foreach($inventories as $inventory)
        {
          $inventory->total = $inventory->to_total - $inventory->from_total;
        }
      
        return view('pages.parts.show')
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
        $part = Part::find($id);
        return view('pages.parts.edit')->with('part', $part);
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
        $this->validate($request, [
          'part_name' => 'required',
          'part_serial' => 'required',
          'part_color' => 'required',
          'part_version' => 'required',
          'part_weight' => 'required',
        ]);
      
        // Create Part
        $part = Part::find($id);
        $part->part_name = $request->input('part_name');
        $part->part_serial = $request->input('part_serial');
        $part->part_color = $request->input('part_color');
        $part->part_weight = $request->input('part_weight');
        $part->part_version = $request->input('part_version');
        if($request->get('part_cleaned') == null) {
          $part->part_cleaned = 0;
        } else {
          $part->part_cleaned = 1;
        }
        $part->save();
        return redirect("/parts")->with('success', 'Part Created!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
        $locations = Location::all();
        $part = Part::find($id);
        $deleted_inventories = 0;
        foreach($locations as $location)
        {
          $inventory = Inventory::where([
            ['part_id', '=', $part->id],
            ['location_id', '=', $location->id]
          ]);
          Inventory::destroy($inventory->id);
          $deleted_inventories++;
        }
        
        
        Part::destroy($id);
        return redirect()->route('parts.index')->with('success', 'Part '.$part->part_serial.' deleted. '.$deleted_inventories.' were deleted.');

    }
}
