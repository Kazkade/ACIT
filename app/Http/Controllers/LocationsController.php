<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Location;
use App\Part;
use App\Transfer;
use App\Inventory;

class LocationsController extends Controller
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
        $locations = Location::all();
        return view('pages.locations.index')
          ->with('locations', $locations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
          'location_name' => 'required',
          'location_description' => 'required',
        ]);
      
        // Create Part
        $location = new Location;
        $location->location_name = $request->input('location_name');
        $location->location_description = $request->input('location_description');
      
        // Default Location
        if($request->input('location_default') == null) {
          $location->location_default = 0;
        } else {
          $location->location_default = 1;
        }
      
        // Special Location
        if($request->input('location_special') == null) {
          $location->location_special = 0;
        } else {
          $location->location_special = 1;
        }
      
        // Restricted Location
        if($request->input('location_restricted') == null) {
          $location->location_restricted = 0;
        } else {
          $location->location_restricted = 1;
        }
      
        // Save
        $location->save();
      
        // Create Inventories
        $location = Location::orderBy('updated_at', 'desc')->first();
        $parts = Part::all();
        $created_inventories = 0;
        foreach($parts as $part) 
        {
          $inventory = new Inventory;
          $inventory->part_id = $part->id;
          $inventory->location_id = $location->id;
          $inventory->from_total = 0;
          $inventory->to_total = 0;
          $inventory->save();
          $created_inventories++;
        }
      
        return redirect()->route('locations.index')
          ->with('success', 'Location '.$location->location_name.' created! '.$created_inventories." inventories created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $locations = Location::find($id);
        $parts = Part::all();
        $transfers = Transfer::all();
      
        foreach($parts as $part) 
        {
          $part->has_transfers = 0;
          $part->to_transfers = 0;
          $part->current_count = 0;
          $part->from_transfers = 0;
          foreach($transfers as $transfer)
          {
            if($part->id == $transfer->part_id)
            {
              // This part has transers.
              $part->has_transfers = 1;
              // Increase To Transfers and Increase Current
              if($transfer->to_location_id == $locations->id)
              {
                $part->to_transfers += $transfer->quantity;
                $part->current_count += $transfer->quantity;
              }
              // Increase From Transfers and Decrase Current
              if($transfer->from_location_id == $locations->id)
              {
                $part->from_transfers += $transfer->quantity;
                $part->current_count -= $transfer->quantity;
              }
              
            }
          }
        }
      
        return view('pages.locations.show')
          ->with('locations', $locations)
          ->with('parts', $parts)
          ->with('transfers', $transfers);
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
        $location = Location::find($id);
        $location->location_name = $request->input('location_name');
        $location->location_description = $request->input('location_description');
      
        // Default Location
        if($request->input('location_default') == null) {
          $location->location_default = 0;
        } else {
          $location->location_default = 1;
        }
      
        // Special Location
        if($request->input('location_special') == null) {
          $location->location_special = 0;
        } else {
          $location->location_special = 1;
        }
      
        // Restricted Location
        if($request->input('location_restricted') == null) {
          $location->location_restricted = 0;
        } else {
          $location->location_restricted = 1;
        }
      
        // Save
        $location->save();
        return redirect('/locations')
          ->with('success', 'Location '.$location->location_name.' updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
        // Destroy Inventories
        $location = Location::find($id);
        $parts = Part::all();
        $deleted_inventories = 0;
        foreach($parts as $part) 
        {
          $inventory = Inventory::where([
            ['part_id', '=', $part->id],
            ['location_id', '=', $location->id]
          ])->first();
          Inventory::destroy($inventory->id);
          $deleted_inventories++;
        }
      
        Location::destroy($id);
        return redirect()->route('locations.index')
          ->with('success', 'Location '.$location->location_name.' deleted! '.$deleted_inventories." inventories deleted.");
      
    }
}
