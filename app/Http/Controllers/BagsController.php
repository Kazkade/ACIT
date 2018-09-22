<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\PermissionEnforcer;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Location;
use App\Part;
use App\Bag;
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
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unbag($id)
    {
      // Get bag from ID
      $bag = Bag::find($id);
      // Get backstock ID
      $backstock = DB::table('locations')->where('location_name', '=', 'Backstock')->first();
      $backstock_id = $backstock->id;
      // update Inventory
      $inventory = DB::table('inventories')
        ->where('part_id', '=', $bag->part_id)
        ->where('location_id', '=', $backstock_id)
        ->increment('to_total', $bag->quantity);
      
      Bag::destroy($id);

      return redirect("/bags")
        ->with('success', 'Parts was unbagged.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      if(AdminEnforcer::Enforce()){
        return redirect()->route('unauthorized');  
      }
      
      // Get bag with ID
      $bag = Bag::destroy($id);

      return redirect("/bags")
        ->with('success', 'Bag was destroyed.');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function hand_deliver($id)
    {
      
      if(AdminEnforcer::Enforce()){
        return redirect()->route('unauthorized');  
      }
      
      // Get bag with ID
      $bag = Bag::find($id);
      
      // Create and complete delivery.
      $delivery = new Delivery();
      $delivery->user_id = Auth::user()->id;
      $delivery->save();
      $delivery = DB::table('deliveries')
        ->orderBy('updated_at', 'desc')
        ->first();
      
      // Create & Fill new MO.
      $new_mo = new Order;
      $new_mo->part_id = $bag->part_id;
      $new_mo->mo = "HD/".$bag->id;
      $new_mo->delivery_id = $delivery->id;
      $new_mo->quantity = $bag->quantity;
      $new_mo->filled = $bag->quantity;
      $new_mo->closed = 1;
      $new_mo->priority = 0;
      $new_mo->save();
      
      Bag::where('id', '=', $id)
        ->update(['delivered' => 1, 'delivered_by' => Auth::user()->id]);

      return redirect("/deliveries/all")
        ->with('success', 'Bag was hand delivered!');
    }
    
}
