<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Transfer;
use App\Location;
use App\Part;
use App\Inventory;
use App\Bag;

class TransfersController extends Controller
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
        //
        $transfers = DB::table('transfers')
          ->orderBy('updated_at', 'desc')
          ->paginate(50);
        $locations = Location::all();
        $users = User::all();
        $parts = Part::all();
        return view('pages.transfers.index')
          ->with('transfers', $transfers)
          ->with('locations', $locations)
          ->with('parts', $parts)
          ->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $locations = Location::all();
      $parts = Part::all();	  
      
      $transfers = DB::table('transfers')
        ->join('parts', 'parts.id', '=', 'transfers.part_id')
        ->join('users', 'users.id', '=', 'transfers.user_id')
        ->select('parts.*', 'transfers.*', DB::raw('CONCAT(`users`.`first_name`, " ",`users`.`last_name`) as "tech_name"'))
        ->orderBy('transfers.updated_at', 'desc')
        ->take(20)
        ->get();
      
      foreach($transfers as $transfer)
      {
        foreach($locations as $location)
        {
          if($location->id == $transfer->to_location_id)
          {
            $transfer->to_name = $location->location_name;
          }
          
          if($location->id == $transfer->from_location_id)
          {
            $transfer->from_name = $location->location_name;
          }
        }
      }

      return view('pages.transfers.create')	 
        ->with('locations', $locations)
        ->with('transfers', $transfers)
        ->with('parts', $parts);
    }
  
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function reverse($id)
    {
      // Find Transfer
      $transfer = Transfer::find($id);
      // Create Duplicate transfer with reversed locations.
      $reverse = new Transfer();
        $reverse->part_id = $transfer->part_id;
        $reverse->to_location_id = $transfer->to_location_id;
        $reverse->from_location_id = $transfer->from_location_id;
        $reverse->quantity = $transfer->quantity;
      $reverse->user_id = Auth::user()->id;
      $reverse->reversal = 1;
      $reverse->save();

      // Update From Inventory
      $from_inventory = DB::table('inventories')
        ->where('part_id', '=', $reverse->part_id)
        ->where('location_id', '=', $reverse->from_location_id)
        ->decrement('from_total', $reverse->quantity);

      // Update Pass Inventory
      $reverse_inventory = DB::table('inventories')
        ->where('part_id', '=', $reverse->part_id)
        ->where('location_id', '=', $reverse->to_location_id)
        ->decrement('to_total', $reverse->quantity);

      return redirect()->route('transfers.index')
        ->with('success','Transfer reversed!');
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
          'part_serial' => 'required',
          'bag_amount' => 'required',
          'to_location_id' => 'required',
          'from_location_id' => 'required',
        ]);
      
        $part = DB::table('parts')
          ->where('id', '=', $request->part_id)
          ->first();
        
        if(
          $request->input('bag_amount') <= 0 && 
          $part->part_cleaned == 0 &&
          $request->transfer_type == 1
        )
        {
          return redirect()->route('transfers.create', ['transfer_type' => $request->transfer_type])
          ->with('success','Bagging amount can\'t be 0.');
        }
      
        if(
          $request->input('bag_amount') <= 0 && 
          $request->transfer_type == 2
        )
        {
          return redirect()->route('transfers.create', ['transfer_type' => $request->transfer_type])
          ->with('success','Bagging amount can\'t be 0.');
        }
      
        // Create Part
        $pass_transfer = new Transfer;
        $pass_transfer->part_id = $request->input('part_id');
        $pass_transfer->user_id = Auth::user()->id;
        $pass_transfer->quantity = $request->input('quantity');
        $pass_transfer->from_location_id = $request->input('from_location_id');
        $pass_transfer->to_location_id = $request->input('to_location_id');
        
        $fail_transfer = new Transfer;
        $fail_transfer->part_id = $request->input('part_id');
        $fail_transfer->user_id = Auth::user()->id;
        $fail_transfer->quantity = $request->input('fails');
        $fail_transfer->from_location_id = $request->input('from_location_id');
        $fail_transfer->to_location_id = $request->input('fail_location_id');
      
        // Update From Inventory
        $from_inventory = DB::table('inventories')
          ->where('part_id', '=', $pass_transfer->part_id)
          ->where('location_id', '=', $pass_transfer->from_location_id)
          ->increment('from_total', $pass_transfer->quantity + $fail_transfer->quantity);
      
        // Update Pass Inventory
        $pass_inventory = DB::table('inventories')
          ->where('part_id', '=', $pass_transfer->part_id)
          ->where('location_id', '=', $pass_transfer->to_location_id)
          ->increment('to_total', $pass_transfer->quantity);
      
        // Update Fail Inventory
        $fail_inventory = DB::table('inventories')
          ->where('part_id', '=', $fail_transfer->part_id)
          ->where('location_id', '=', $fail_transfer->to_location_id)
          ->increment('to_total', $fail_transfer->quantity);
        
        // Get Backstock Location ID
        $backstock_location_id = DB::table('locations')
        ->where('location_name', '=', 'Backstock')
        ->get();
      
        $backstock_location_id = $backstock_location_id[0]->id;
      
        if($request->input('to_location_id') == $backstock_location_id)
        {
          for($i = 0; $i < (int)$request->input('created_bags'); $i++)
          {
            $bags = new Bag();
            $bags->created_by = Auth::user()->id;
            $bags->part_id = $request->input('part_id');
            $bags->delivered = 0;
            $bags->quantity = $request->input('bag_amount');
            $bags->save();

            // Update From Inventory
            $from_inventory = DB::table('inventories')
              ->where('part_id', '=', $pass_transfer->part_id)
              ->where('location_id', '=', $pass_transfer->to_location_id)
              ->decrement('to_total', $request->input('bag_amount'));
          }
        }
      
        // Save Everything
        if($pass_transfer->quantity > 0)
        {
          $pass_transfer->save();
        }
        if($fail_transfer->quantity > 0)
        {
          $fail_transfer->save();
        }
        
        if($part->part_cleaned == 1)
        {
          return redirect()->route('transfers.create', ['transfer_type' => $request->transfer_type])
          ->with('success','Transfer recorded!');
        }
        else 
        {
          return redirect()->route('transfers.create', ['transfer_type' => $request->transfer_type])
          ->with('success','Transfer Recorded! '.$request->input('created_bags').' bag(s) created.');
        }
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transfers = Transfer::find($id);
        $locations = Location::all();
        $users = User::all();
        $parts = Part::all();
        
        return view('pages.transfers.show')
          ->with('transfers', $transfers)
          ->with('locations', $locations)
          ->with('parts', $parts)
          ->with('users', $users);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
