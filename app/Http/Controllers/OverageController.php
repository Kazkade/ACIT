<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Part;
use App\Overage;
use App\Order;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class OverageController extends Controller
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
     
      $overages = DB::table('overages')
        ->join('parts', 'parts.id', '=', 'overages.part_id')
        ->select('overages.*', 'parts.*', DB::raw('`overages`.`id` as "overage_id"'))
        ->get();
      
      return view('pages.overages.index')
        ->with('overages', $overages);
    }
  
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function resolve($id)
    {
      Overage::where('id', '=', $id)
        ->update(['resolved' => 1]);
      
      $overage = Overage::find($id);
      $last_order = DB::table('orders')
        ->orderBy('id', 'desc')
        ->first();
      
      $new_mo = new Order;
      $new_mo->part_id = $overage->part_id;
      $new_mo->mo = "OV/".$overage->id;
      $new_mo->delivery_id = $overage->delivery_id;
      $new_mo->quantity = $overage->quantity;
      $new_mo->filled = $overage->quantity;
      $new_mo->closed = 1;
      $new_mo->priority = 0;
      $new_mo->save();
      
      $resolved_mo = DB::table('orders')
        ->orderBy('id', 'desc')
        ->first();
      
      Overage::where('id', '=', $id)
        ->update(['ov_mo' => $resolved_mo->id]);

      // The creation form is on the sidebar for admins.
      return redirect()->route('overages.index')->with('success', 'Overage was resolved!');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unresolve($id)
    {
      Overage::where('id', '=', $id)
        ->update(['resolved' => 0]);
      // The creation form is on the sidebar for admins.
      return redirect()->route('overages.index')->with('error', 'Overage was unresolved.');
    }
  
    
}

