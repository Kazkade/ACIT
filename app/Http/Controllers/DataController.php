<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Transfer;
use App\Bag;
use App\Delivery;
use App\Overage;
use App\Inventory;

class DataController extends Controller
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
    public function reset_inventory($authorization_code)
    {
      $reset = 1;
      $codes = DB::table('database_reset_codes')->get();
      foreach($codes as $code)
      {
        if($code == $authorization_code)
        {
          $reset = 1;
        }
      }
      
      if($reset == 1)
      {
        $inventories = Inventory::all();
        foreach($inventories as $inventory)
        {
          $inventory->to_total = 0;
          $inventory->from_total = 0;
          $inventory->save();
        }
        
        Transfer::truncate();
        Bag::truncate();
        Delivery::truncate();
        Overage::truncate();
        
      }
      else
      {
        if(Auth::user()->admin == 0){
          return redirect('/')->with('error', "You're not an admin.");
        }
        else
        {
          return redirect('/')->with('error', "The passcode was incorrect.");
        }
      }
      
      return redirect('/')->with('success', "All inventory entries reset/deleted.");
    }
  
}
