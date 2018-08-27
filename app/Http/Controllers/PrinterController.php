<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Printer;
use App\PrintProfile;

class PrinterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Create a new printer. 
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
     
        $printer = new Printer();
        $printer->name = $request->printer_name;
        $printer->active = 1;
        $printer->save();

        $profile_counter = 0;
        // Get last printer entered.
        $printer = DB::table('printers')
          ->orderBy('updated_at', 'DESC')
          ->first();

        foreach(DB::table('parts')->get() as $part)
        {
          $profile = new PrintProfile();
          $profile->printer_id = $printer->id;
          $profile->part_id = $part->id;
          $profile->lead_time = 0;
          $profile->active = 0;
          $profile->save();
          $profile_counter++;
        }

        return redirect("/configuration")->with('success', $printer->name.' Created! '.$profile_counter.' Part Profiles created as inactive.');
    }
    /**
     * Toggle whether the printer is active or not
     * and count how many profiles are affected by this change.
     *
     * @param $id = Printer ID
     * @return \Illuminate\Http\Response
     */
    public function toggle($id)
    {
        $printer = DB::table('printers')
          ->where('id', '=', $id)
          ->first();
        
        $active = 0;
        if($printer->active == 0)
        {
          $active = 1;
        }
        
        // Update that printer.
        DB::table('printers')
          ->where('id', '=', $id)
          ->update(['active' => $active]);
        
        // Get how many parts were affected.
        $profile_count = 0;
        $print_profile = DB::table('print_profiles')
          ->where('printer_id', '=', $printer->id)
          ->get();
      
        foreach($print_profile as $profile)
        {
          if($profile->active == 1)
          {
            $profile_count++;
          }
          
        }
      
        return redirect("/configuration")->with('success', $printer->name.' toggled. '.$profile_count." printer profiles will be affected by this change.");
    }
    /**
     * Destroy the printer and profiles associated with it.
     *
     * @param $id = Printer ID
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
       
       $printer = DB::table('printers')->where('id', '=', $id)->first();
       $printer_name = $printer->name;
       Printer::destroy($id);
      
       $profile_counter = 0;
       foreach(DB::table('print_profiles')->where('printer_id', '=', $id)->get() as $profile)
       {
         PrintProfile::destroy($profile->id);
         $profile_counter++;
       }

        return redirect("/configuration")->with('success', $printer_name.' deleted! '.$profile_counter.' Part Profiles were also deleted.');
    }
  
}


