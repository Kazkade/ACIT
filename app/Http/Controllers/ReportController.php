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
      // Get total data.
      $parts = DB::table('parts')->get();
      $inventories = DB::table('inventories')->get();
      $bags = DB::table('bags')->get();
      $orders = DB::table('orders')->get();
      
      // Get Inventory IDs
      $collections_id = DB::table('locations')->where('location_name', '=', 'Collections')->first(); $collections_id = $collections_id->id;
      $processing_id = DB::table('locations')->where('location_name', '=', 'Processing')->first(); $processing_id = $processing_id->id;
      $backstock_id = DB::table('locations')->where('location_name', '=', 'Backstock')->first(); $backstock_id = $backstock_id->id;
      $fails_id = DB::table('locations')->where('location_name', '=', 'Fails')->first(); $fails_id = $fails_id->id;
      $inhouse_id = DB::table('locations')->where('location_name', '=', 'InHouse')->first(); $inhouse_id = $inhouse_id->id;
      
      foreach($parts as $part)
      {
        // Get full inventories.
        foreach($inventories as $inventory)
        {
          if($inventory->part_id && $part->id)
          {
            if($inventory->location_id == $collections_id) 
            {
              $part->collections = $inventory->to_total - $inventory->from_total;
            }
                      
            if($inventory->location_id == $processing_id) 
            {
              $part->processing = $inventory->to_total - $inventory->from_total;
            }
                      
            if($inventory->location_id == $backstock_id) 
            {
              $part->backstock = $inventory->to_total - $inventory->from_total;
            }
                      
            if($inventory->location_id == $fails_id) 
            {
              $part->fails = $inventory->to_total - $inventory->from_total;
            }
            if($inventory->location_id == $inhouse_id) 
            {
              $part->inhouse = $inventory->to_total - $inventory->from_total;
            }
          }
        }
      }
      
      // Build Report
      $table = array();
      foreach($parts as $part)
      {
        $row = (object)[];
        $row->part_name = $part->part_name;
        $row->part_serial = $part->part_serial;
        $row->part_color = $part->part_color;
        $row->part_mass = $part->part_mass;
        $row->part_waste = $part->part_waste;
        $row->parts_created = $part->collections;
        $row->parts_failed = $part->fails;
        $row->passed_filament = $part->backstock * ($part->part_mass);
        $row->fallout_filament = $part->fails * ($part->part_mass);
        $row->scrap_filament = $part->collections * ($part->part_waste);
        $row->inhouse_filament = $part->inhouse * ($part->part_mass);
        $row->total_filament = $part->collections * ($part->part_mass + $part->part_waste);
        array_push($table, $row);
      }
      
      // Seperate Rows by Color
      // // Get filament colors among parts.
      $filament_colors = array();
      foreach($table as $row)
      {
        if(!in_array($row->part_color, $filament_colors))
        {
          array_push($filament_colors, $row->part_color);
        }
      }
      
      $separated = array();
      // Cycle through colors first and
      foreach($filament_colors as $color)
      {
        foreach($table as $row)
        {
          if($row->part_color == $color)
          {
            array_push($separated, $row);
          }
        } 
      }
     
      return view('pages.reports.filament_usage')
        ->with('colors', $filament_colors)
        ->with('report', $separated);
    }
  
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */    
    public function weekly_scrap()
    {
      // Get total data.
      $parts = DB::table('parts')->get();

      // Get first transfer.
      $earliest_date = DB::table('transfers')
        ->select(DB::raw("MAX(DATE(`updated_at`)) as earliest"))
        ->first();
      // Get earliest date from variable.
      $earliest_date = $earliest_date->earliest;
      // Get next wednesday.
      $first_wednesday = date('Y-m-d', strtotime("next wednesday", strtotime($earliest_date)));
      
      // This is a huge query. Can take some time.
      $transfers = DB::select('
        SELECT 
        T.`part_id` AS "part_id", 
        T.`week_number` AS "week_number", 
        T.`to_location_id` AS "to_location_id", 
        T.`from_location_id` AS "from_location_id", 
        sum(T.`quantity`) AS "quantity"
        FROM (
            SELECT 
            `part_id`,
            `to_location_id`,
            `from_location_id`,
            CEILING(DATEDIFF(DATE(:first_wednesday), DATE(`updated_at`))/7)-1 AS "week_number",
            `quantity`
            FROM `transfers`
        ) AS T
        GROUP BY
        part_id,
        week_number
      ',
       array(
         'first_wednesday' => $first_wednesday
       ));
          
      // Get Inventory IDs
      $collections_id = DB::table('locations')->where('location_name', '=', 'Collections')->first(); $collections_id = $collections_id->id;
      $processing_id = DB::table('locations')->where('location_name', '=', 'Processing')->first(); $processing_id = $processing_id->id;
      $backstock_id = DB::table('locations')->where('location_name', '=', 'Backstock')->first(); $backstock_id = $backstock_id->id;
      $fails_id = DB::table('locations')->where('location_name', '=', 'Fails')->first(); $fails_id = $fails_id->id;
      $inhouse_id = DB::table('locations')->where('location_name', '=', 'InHouse')->first(); $inhouse_id = $inhouse_id->id;
      
      if(count($transfers) == 0) 
      {
        $filament_colors = array();
        $report = array();
        return redirect('/')
        ->with('error', "There was nothing to report.")
        ->with('colors', $filament_colors)
        ->with('report', $report);
      }
      
      // Build Report
      $table = array();
      foreach($transfers as $transfer)
      {
        foreach($parts as $part)
        {
          if($transfer->to_location_id == $fails_id && $transfer->part_id == $part->id)
          {
            $part->scrap += $transfer->quantity * ($part->part_waste + $part->part_mass);
          }
          if($transfer->from_location_id == $fails_id && $transfer->part_id == $part->id)
          {
            $part->scrap -= $transfer->quantity * ($part->part_waste + $part->part_mass);
          }
          array_push($table, $part);
        }

      }
      
      // Seperate Rows by Color
      // // Get filament colors among parts.
      $filament_colors = array();
      foreach($table as $row)
      {
        if(!in_array($row->part_color, $filament_colors))
        {
          array_push($filament_colors, $row->part_color);
        }
      }
      
      $report = array();
      // Cycle through colors first and
      foreach($filament_colors as $color)
      {
        foreach($table as $row)
        {
          if($row->part_color == $color)
          {
            array_push($separated, $row);
          }
        } 
      }
      
      die(var_dump($report));
      
      return view('pages.reports.weekly_scrap')
        ->with('colors', $filament_colors)
        ->with('report', $report);
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
