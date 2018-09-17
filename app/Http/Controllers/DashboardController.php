<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Location;
use App\Part;
use App\Transfer;

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
      // Enable query logging for Debugging.
      DB::enableQueryLog();
      #########################################################################
      # Getting the last 30 days.
      #########################################################################
      // Set up variables.
      $days_back = 30; // Change this to change the amount of days back you want to grab.
      // Days is an array of the last X days. 30 is default.
      $days = array();
      // SQL Days is a workaround for querying. $days is better formatted.
      $sql_days = array();
      // Fill the arrays.
      for($i = 0; $i < 30; $i++) 
      {
          $days[] = date("m/d/y", strtotime('-'. $i .' days'));
          $sql_days[] = date("y-m-d", strtotime('-'. $i .' days'));
      }
      // Get "Collections" location.
      $collections_id = DB::table('locations')
        ->where('location_name', '=', 'Collections')
        ->first();
      // Get that location's ID.
      $collections_id = $collections_id->id;
      
      #########################################################################
      # Get Filament information.
      #########################################################################
      $filaments = DB::table('filaments')->get();
      $filament_by_production = DB::table('inventories')
      ->join('parts', 'parts.id', '=', 'inventories.part_id')
      ->join('filaments', 'filaments.filament_name', '=', 'parts.part_color')
      ->select(DB::raw('SUM(`from_total`+ `to_total`) as "total"'), 'parts.part_color', 'filaments.background_color')
      ->where('location_id', '=', $collections_id)
      ->groupBy('part_color')
      ->get();
      
      #########################################################################
      # Production Data
      #########################################################################
      // Initialize Array.
      
      $production = DB::table('transfers')
      ->join('parts', 'parts.id', '=', 'transfers.part_id')
      ->select(DB::raw('SUM(`transfers`.`quantity`) as "total"'), 'parts.part_color', DB::raw('DATEDIFF(CURDATE(), `transfers`.`updated_at`) as "day"'))
      ->where('transfers.from_location_id', '=', $collections_id)
      ->groupBy('day', 'parts.part_color')
      ->take(30)
      ->get();
      
      //print('<pre>'.print_r($production, true).'</pre>');
      
      foreach($filaments as $filament)
      {
        for($i = 0; $i < count($days); $i++)
        {
          $filament->production[$i] = (object) array('day' => $i, 'total' => 0);
          foreach($production as $prod)
          {
            if($prod->part_color == $filament->filament_name && $prod->day == $i)
            {
              $filament->production[$i] = $prod;
            }
          }
        }
        
        $filament->production = array_reverse($filament->production);
      }
        
      //print('<pre>'.print_r($filaments, true).'</pre>');
      //print_r($filaments);
      //echo "<br>";
      //dd(DB::getQueryLog());
      
      #########################################################################
      # Production Data Filtered by Filament
      #########################################################################
      // Initialize Array.
      $prod_fil = array();
      // Get the sum of all transfers in the last X days in SQL_Days (from before).
      foreach($sql_days as $day)
      {
        $per_day = array();
        $produced = DB::table('transfers')
          ->join('parts', 'parts.id',  '=', 'transfers.part_id' )
          ->select(DB::raw('SUM(`transfers`.`quantity`) as "total"'), 'parts.part_color')
          ->where('transfers.updated_at', 'like', '%'.$day.'%')
          ->where('transfers.from_location_id', '=', $collections_id)
          ->groupBy('parts.part_color')
          ->get();
        
        
        // It it's not null (no value), set the value to 0 as default to avoid errors when sending to the view.
        foreach($filaments as $filament)
        {
          foreach($produced as $each)
          {
            
          }
        }
        // Push that production value to the array.
        array_push($prod_fil, $per_day);
      }
      
      //echo '<pre>' . json_encode($prod_fil, JSON_PRETTY_PRINT) . '</pre>';
      //die();


      
      #########################################################################
      # Debugging
      #########################################################################
      //die(print_r($filament_by_production));
      //dd(DB::getQueryLog());
      
      #########################################################################
      # Messages
      #------------------------------------------------------------------------
      # Thesee are used to relay to admin information about unresolved issues
      # that are more often than not, related to the manual bridge between Odoo
      # and ACIT. This can better be suited to be made into its' own class,
      # then invoked in the layout. But for now, it can serve its' purpose
      # just fine this way.
      #########################################################################
      $messages = array();
      // Get a list of all unresolved overages.
      $overages = DB::table('overages')
        ->join('parts', 'parts.id', '=', 'overages.part_id')
        ->where('resolved', '=', 0)
        ->get();
      // Then create an object and append it to the $messages object array.
      foreach($overages as $overage)
      {
        $message = (object) array();
        $message->alert_type = "danger";
        $message->header = "Unresolved Overages";
        $message->message = "There's are unrsolved overages.";
        $message->link = "/overages";
        array_push($messages, $message);
      }
      
      #########################################################################
      # Return
      /*-----------------------------------------------------------------------
      # This returns several objects to the Dashboard view.
      */#######################################################################
      return view('pages.dashboard.index')
        ->with('filament_by_production', $filament_by_production)
        ->with('filaments', $filaments)
        ->with('messages', $messages)
        ->with('days', array_reverse($days))
        ->with('production_by_filament', $prod_fil);
    }
  
}
