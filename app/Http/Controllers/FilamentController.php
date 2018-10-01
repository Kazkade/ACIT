<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Filament;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class FilamentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      if(!\App\PermissionEnforcer::Protect("filaments_create")) { return response("Unauthorized", 401); }
      //
      $this->validate($request, [
        'filament_name' => 'required',
        'background_color' => 'required',
        'text_color' => 'required',
      ]);        

      $filament = new Filament();
      
      $filament->filament_name = (string)$request->input('filament_name');
      $filament->background_color = (string)$request->input('background_color');
      $filament->text_color = (string)$request->input('text_color');
      $filament->active = 1;
      
      $filament->save();
      
      return redirect()->route('configuration.index')
        ->with('success', "Filament created!");
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggle($id)
    {    
        if(!\App\PermissionEnforcer::Protect("filaments_moratorium")) { return response("Unauthorized", 401); }
        $filament = DB::table('filaments')->where('id', '=', $id)->first();
      
        $active = 0;
        if($filament->active == 0)
        {
          $active = 1;
        }
        
        // Update that printer.
        DB::table('filaments')
          ->where('id', '=', $id)
          ->update(['active' => $active]);
      
        return redirect()->route('configuration.index')->with('success', "Filament updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
      if(!\App\PermissionEnforcer::Protect("filaments_delete")) { return response("Unauthorized", 401); }
        $filament = DB::table('filaments')
          ->where('id', '=', $id)
          ->delete();
      
        return redirect()->route('configuration.index')->with('success', "Filament deleted.");
    }
  
  
}
