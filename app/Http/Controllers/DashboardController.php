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

class DashboardController extends Controller
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
      DB::enableQueryLog();
      $days = array();
      $sql_days = array();
      for($i = 0; $i < 30; $i++) 
      {
          $days[] = date("m/d/y", strtotime('-'. $i .' days'));
          $sql_days[] = date("y-m-d", strtotime('-'. $i .' days'));
      }
      
      $collections_id = DB::table('locations')
        ->where('location_name', '=', 'Collections')
        ->first();
      
      $collections_id = $collections_id->id;
      
      $production = array();
      foreach($sql_days as $day)
      {
        $produced = DB::table('transfers')
          ->select(DB::raw('SUM(`quantity`) as "total"'))
          ->where('updated_at', 'like', '%'.$day.'%')
          ->where('from_location_id', '=', $collections_id)
          ->first();
        if($produced->total == null)
        {
          $produced->total = 0;
        }
        array_push($production, $produced->total);
      }
      //die(print_r($production));
      //dd(DB::getQueryLog());
      return view('pages.dashboard.index')
        ->with('days', array_reverse($days))
        ->with('production', array_reverse($production));
    }

}
