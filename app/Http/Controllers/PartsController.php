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
use App\Printer;
use App\PrintProfile;

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
      
      // Comment this out to avoid duplicate inventories.
      // This will, when run, create inventories for every part and every locations.
      // You should only need this when you've truncated the inventories, parts, and transfers tables
      // and are re-uploading the CSV for the parts list.
      // This process can take several minutes to complete.
      /*
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
      
      // Comment this out to avoid duplicate profiles.
      // This will, when run, create profiles for every part and every printer.
      // You should only need this when you've truncated the profiles tables.
      // This process can take several minutes to complete.
      /*
      $parts = Part::all();
      $printers = Printer::all();
      foreach($parts as $part)
      {
        foreach($printers as $printer)
        {
          $profile = new PrintProfile;
          $profile->printer_id = $printer->id;
          $profile->part_id = $part->id;
          $profile->lead_time = 0;
          $profile->prints = 0;
          $profile->active = 0;
          $profile->save();
        }
          
      }
      */
      
      #### Actual Start ##############
      $parts = DB::table('parts')
        ->orderBy('id', 'asc')
        ->get();
      
      $printers = DB::table('printers')
        ->where('active', '=', 1)
        ->get();
      
      $filaments = DB::table('filaments')->get();
      
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
      
      // Consolodate Fields
      foreach($parts as $part)
      {
        foreach($inventories as $inventory)
        {
          if($inventory->part_id == $part->id)
          {
            $part->inventory = $inventory->inventory;
            $part->total = $inventory->inventory;
          }
        }
        $part->bag_count = 0;
        foreach(DB::table('bags')->where('delivered', '=', 0)->get() as $bag)
        {
            if($bag->part_id == $part->id)
            {
              $part->bag_count += 1;
              $part->total += $bag->quantity;
            }
        }
      }
      // $posts = DB::select('SELECT * FROM parts');
      return view('pages.parts.index')
        ->with('parts', $parts)
        ->with('printers', $printers)
        ->with('filaments', $filaments);
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
          'part_mass' => 'required',
        ]);
      
        // Create Part
        $part = new Part;
        $part->part_name = $request->input('part_name');
        $part->part_serial = $request->input('part_serial');
        $part->part_color = $request->input('part_color');
        $part->part_mass = $request->input('part_mass');
        $part->part_version = $request->input('part_version');
        if($request->get('part_cleaned') == null) {
          $part->part_cleaned = 0;
        } else {
          $part->part_cleaned = 1;
        }
        $part->print_time = $request->input('print_time');
        $part->recommended_bagging = $request->input('rec_bagging');
        $part->save();
      
        // Retreive this Part's ID
        $part = Part::orderBy('id', 'DESC')->first();  
      
        // Create Inventories for part.
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
      
        // Create Profiles for part.
        $printers = PrintProfile::all();
        foreach($printers as $printer)
        {
          $profile = new PrintProfile;
          $profile->printer_id = $printer->id;
          $profile->part_id = $part->id;
          $profile->lead_time = 0;
          $profile->prints = 0;
          $profile->active = 0;
          $profile->save();
        }
        
        return redirect("/parts")->with('success', 'Part '.$part->part_serial.' Created!');
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
          $inventory->total = (int)$inventory->to_total - (int)$inventory->from_total;
        }
        
        ### Getting Fail Information ##############################
        $collections_location = DB::table('locations')
          ->where('location_name', '=', 'Collections')
          ->orderBy('updated_at', 'desc')
          ->first();
      
        $fail_location = DB::table('locations')
          ->where('location_name', '=', 'Fails')
          ->orderBy('updated_at', 'desc')
          ->first();
      
        $fails = DB::table('inventories')
          ->where('part_id', '=', $part->id)
          ->where('location_id', '=', $fail_location->id)
          ->orderBy('updated_at', 'DESC')
          ->first();
      
        // Fail Rate
        $passes = DB::table('inventories')
          ->where('part_id', '=', $part->id)
          ->where('location_id', '=', $collections_location->id)
          ->orderBy('updated_at', 'DESC')
          ->first();
      
        set_error_handler(function () {
            return 0;
        });
      
        // Fail Percentage
        $part->fail_rate = ($fails->to_total - $fails->from_total == 0) ? 0.00 : ($fails->to_total - $fails->from_total == 0) / $passes->from_total;
        
        
      
        // Grams of waste.
        $part->total_waste = $part->part_mass * ($fails->to_total - $fails->from_total);
      
        // Build bags array. 
        $bags = DB::table('bags')
          ->select('*')
          ->where('part_id', '=', $id)
          ->where('delivered', '=', 0)
          ->get();
        // 
        $users = DB::table('users')
          ->select('*')
          ->get();
        // 
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
        
        // Get Orders, Deliveries, and Demand
        $orders = DB::table('orders')
          ->select(DB::raw('SUM(`quantity`) - SUM(`filled`) as "remaining", SUM(`quantity`) as "ordered", SUM(`filled`) as "delivered"'))
          ->where('part_id', '=', $part->id)
          ->first();
      
        $part->ordered = $orders->ordered;
        $part->delivered = $orders->delivered;
        $part->remaining = $orders->remaining;
        
        // Build Profiles for table.
        $profiles = DB::table('print_profiles')
          ->where('part_id', '=', $id)
          ->get();
      
        $printers = DB::table('printers')
        ->where('active', '=', 1)
        ->get();
      
        foreach($printers as $printer)
        {
            foreach($profiles as $profile)
            {
              if($profile->printer_id == $printer->id)
              {
                $printer->profile_active = ($profile->prints > 0) ? 1 : 0;
                $printer->prints = $profile->prints;
                $printer->lead_time = $profile->lead_time;
              }
            }
        }
      
        return view('pages.parts.show')
          ->with('inventories', $inventories)
          ->with('locations', $locations)
          ->with('bags', $bags)
          ->with('part', $part)
          ->with('printers', $printers);
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
     */
    public function update_or_create(Request $request)
    {
      echo "Request was: ".var_dump($request->all);
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
     * Changes moratorium status.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function moratorium($id)
    {
      
        $part = Part::find($id);
        $part->in_moratorium = ($part->in_moratorium == 0) ? 1 : 0;
        $part->save();
        return redirect("/parts/".$id);
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
        $profiles = PrintProfile::all();
        
        $part = Part::find($id);
        $deleted_inventories = 0;
        $deleted_profiles = 0;
        
        foreach($locations as $location)
        {
          $inventory = Inventory::where([
            ['part_id', '=', $part->id],
            ['location_id', '=', $location->id]
          ]);
          Inventory::destroy($inventory->id);
          $deleted_inventories++;
        }
      
        foreach($printers as $printer)
        {
          $profile = PrintProfile::where([
            ['part_id', '=', $part->id],
            ['printer_id', '=', $printer->id]
          ]);
          PrintProfile::destroy($profile->id);
          $deleted_profiles++;
        }
        
        
        Part::destroy($id);
        return redirect()->route('parts.index')->with('success', 'Part '.$part->part_serial.' deleted. '.$deleted_inventories.' inventories were deleted. '.$deleted_profiles.' profiles were deleted.');

    }
}
