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
        $transfers = DB::table('transfers')->paginate(50);
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
      
        $locations = Location::where('location_default', 1)->get();  
        $parts = Part::all();
          
        return view('pages.transfers.create')
          ->with('locations', $locations)
          ->with('parts', $parts);
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
          'quantity' => 'required',
          'fails' => 'required',
          'bag_amount' => 'required',
          'fail_location_id' => 'required',
        ]);
      
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

      
        // Save Everything
        $pass_transfer->save();
        $fail_transfer->save();
      
        return redirect()->route('transfers.create', ['transfer_type' => $request->transfer_type])->with('success','Transfer Recorded!');
        
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
