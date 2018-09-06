<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Location;
use App\Part;
use App\Transfer;
use App\Inventory;

class BagsController extends Controller
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
    public function mark($id)
    {
        $bag = DB::table('bags')
          ->where('id', '=', $id)
          ->update(['marked' => 1]);
      
        $bag = DB::table('bags')
          ->where('id', '=', $id)
          ->get();
      
        return redirect()->route('deliveries.index', ['part_id='.$bag[0]->part_id]);
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unmark($id)
    {
        $bag = DB::table('bags')
          ->where('id', '=', $id)
          ->update(['marked' => 0]);
      
        $bag = DB::table('bags')
          ->where('id', '=', $id)
          ->get();
      
        return redirect()->route('deliveries.index', ['part_id='.$bag[0]->part_id]);
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dismantle($id)
    {
      // Get bag with ID
      $bag = DB::table('bags')
        ->where('id', '=', $id)
        ->first();
      // Get backstock location ID
      $backstock_id = DB::table('locations')
        ->where('location_name', '=', "Backstock")
        ->first();
      $backstock_id = $backstock_id->id;
      // Get backstock location ID
      $bag_location_id = DB::table('locations')
        ->where('location_name', '=', "Bags")
        ->first();
      $bag_location_id = $bag_location_id->id;
      // Get backstock inventory.
      $backstock = DB::table('inventories')
        ->where('location_id', '=', $backstock_id)
        ->where('part_id', '=', $bag->part_id)
        ->first();
      // Adjust inventory and save.
      $backstock->from_total -= $bag->quantity;
      $backstock->save();
      // Delete bag.
      $bag->delete();

      return redirect()->route('bags.index')
        ->with('success', 'Bag was dismantled.');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $users = DB::table('users')
        ->get();
      
      $bags = DB::table('bags')
        ->join('parts', 'parts.id', '=', 'bags.part_id')
        ->select('bags.*', 'parts.part_name', 'parts.part_serial')
        ->orderBy('parts.part_serial', 'asc')
        ->get();
      
      return view('pages.bags.index')
        ->with('users', $users)
        ->with('bags', $bags);
    }

    
}
