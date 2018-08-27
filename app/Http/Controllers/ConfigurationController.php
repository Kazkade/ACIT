<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Part;
use App\Printer;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class ConfigurationController extends Controller
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
      $printers = DB::table('printers')->get();
      return view('pages.configuration.index')
        ->with('printers', $printers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        return redirect()->route('configuration.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect("/configuration")->with('error', 'Something went wrong.');
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
      
        $bags = DB::table('bags')
          ->select('*')
          ->where('part_id', '=', $id)
          ->where('delivered', '=', 0)
          ->get();
      
        $users = DB::table('users')
          ->select('*')
          ->get();
      
        foreach($bags as $bag)
        {
          $bag->user_name = "";
          foreach($users as $user)
          {
            if($bag->created_by == $user->id)
            {
              $bag->user_name = $user->first_name." ".$user->last_name;
            }
          }
        }       
      
        return view('pages.parts.show');
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
