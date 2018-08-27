<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ReportController extends Controller
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
    public function print_list()
    {
        // Query Setup
        $parts = DB::table('parts')->get();
        $orders = DB::table('orders')
          ->groupBy('part_id')
          ->get(); // The variables are actually reversed in the query creator. So > instead of <.
        //die(var_dump($orders));
        $inventories = DB::table('inventories')->get();
        $locations = DB::table('locations')->get();
        $printers = DB::table('printers')->where('active', '=', 1)->get();
        $profiles = DB::table('print_profiles')->get();
        
        //
        foreach($parts as $part)
        {
          ### Setting Up Orders & Inventory #####################################
          $part->on_order = 0;
          $part->remaining = 0;
          // Get Initial Order Totals
          foreach($orders as $order)
          {
              if($order->part_id == $part->id)
              {
                $part->on_order += $order->quantity;
                $part->remaining += ($order->quantity - $order->filled);
              }
          }
          foreach($inventories as $inventory)
          {
            foreach($locations as $location)
            {
              // Very specific "IF".
              if($inventory->part_id == $part->id && $inventory->location_id == $location->id && $location->location_name == "Collections")
              {
                $part->remaining -= $inventory->from_total;
                
              }
            }
          }
                    
          // If negative, return 0;
          if($part->remaining < 0)
          {
            $part->remaining = 0;
          }
          
          ### Setting Up Profiles #####################################
          // Append Profiles
          for($i = 0; $i < count($printers); $i++)
          {
            $part->profile[$i] = $printers[$i];
          }
          
          // Initialize Pods to 10.
          $part->pods = 0;
          
          
          // Loop through printers and profiles.
          // Find the profile that matches each printer in the loop AND the current part in the loop
          // and return that profile information. 
          // This will return $part->profile as an array.
          for($i = 0; $i < count($printers); $i++)
          {
            $pods_array = array();
            // Set Defaults to 0
            $part->profile[$i]->active = 0;
            $part->profile[$i]->lead_time = 0;
            $part->profile[$i]->pods = 0;
            foreach($profiles as $profile)
            {
              if($profile->printer_id == $printers[$i]->id && $profile->part_id == $part->id)
              {
                $part->profile[$i]->active = $profile->active;
                $part->profile[$i]->lead_time = $profile->lead_time;
                $part->profile[$i]->pods = ($profile->prints > 0) ? ceil($part->remaining / $profile->prints) : 1;
                if($part->profile[$i]->pods > $part->pods)
                {
                  $part->pods = $part->profile[$i]->pods;
                }
              } 
            }
          }
          
          foreach($part->profile as $profile)
          {
            if($profile->pods > 0)
            {
              if($profile->pods <= 10 && $profile->pods < $part->pods)
              {
                $part->pods = $profile->pods;
              }
            }
          }
          
        }
      
        // Create Report Object
        $report = array();
        for($i = 0; $i < count($parts); $i++)
        {
          if($parts[$i]->remaining > 0)
          {
            array_push($report, $parts[$i]);
          }
        }
        return view('pages.reports.print_demand')
          ->with('report', $report)
          ->with('printers', $printers);
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delivery_report()
    {
      // not created.
        return view('pages.reports.print_demand');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filament_usage()
    {
      // not created.
        return view('pages.reports.print_demand');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function report_engine($query)
    {
      // not created.
        return view('pages.reports.print_demand');
    }

    
}
