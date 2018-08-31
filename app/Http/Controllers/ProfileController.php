<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Part;
use App\Transfer;
use App\Location;
use App\Inventory;
use App\Printer;
use App\PrintProfile;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class ProfileController extends Controller
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
      
      $parts = DB::table('parts')
        ->orderBy('part_serial', 'asc')
        ->paginate(30);
      $printers = DB::table('printers')->where('active', '=', 1)->get();
      $profiles = DB::table('print_profiles')->get();
      
      foreach($parts as $part)
      {
        for($i = 0; $i < count($printers); $i++)
        {
          
          foreach($profiles as $profile)
          {
            if($profile->printer_id == $printers[$i]->id && $profile->part_id == $part->id)
            {
              //die(var_dump($part->profile));
              $part->profiles[$i] = $profile;
            }
          }
        }
      }
      
      // $posts = DB::select('SELECT * FROM parts');
      return view('pages.profiles.index')
        ->with('parts', $parts)
        ->with('printers', $printers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // There's no reason to use this. Just included for ::resource in routes.
        return view('pages.profiles.index')->with('error', "You don't need to go there.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // There's no reason to use this. Just included for ::resource in routes.
        return view('pages.profiles.index')->with('error', "You don't need to go there.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // There's no reason to use this. Just included for ::resource in routes.
        return view('pages.profiles.index')->with('error', "You don't need to go there.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $part = Part::find($id);
        return view('pages.parts.edit')->with('part', $part);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $part = DB::table('parts')
          ->where('id', '=', $request->input('part_id'))
          ->first();
      
        $printers = DB::table('printers')
          ->where('active', '=', 1)
          ->get();
      
        foreach($printers as $printer)
        {
          $print_profile = DB::table('print_profiles')
          ->where('part_id', '=', $request->input('part_id'))
          ->where('printer_id', '=', $printer->id)
          ->update([
            'prints' => $request->input('profile-prints-'.$printer->id), 
            'lead_time' => $request->input('profile-lead-'.$printer->id),
          ]);
        }
      
        return redirect()->back()
          ->with('part_id', 'Toodaloo')
          ->with('success', 'Profile updated!');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
        // There's no reason to use this. Just included for ::resource in routes.
        return view('pages.profiles.index')->with('error', "You don't need to go there.");
    }
}
