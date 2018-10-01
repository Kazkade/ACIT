<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Filament;
use App\MaintenanceLog;
use App\Transfer;

class MaintenanceController extends Controller
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
        $log = DB::table('maintenance_logs as ml')
          ->join('users', 'users.id', '=', 'ml.user_id')
          ->join('machines', 'machines.id', '=', 'ml.machine_id')
          ->join('printers', 'printers.id', '=', 'machines.id')
          ->select(
            'ml.updated_at', 'ml.task',
            'users.first_name', 'users.last_name',
            'machines.machine_serial', 'machines.id as machine_id',
            'printers.name as printer_name')
          ->orderBy('ml.updated_at', 'desc')
          ->get();
      
        return view('pages.maintenance.index')
          ->with('log', $log);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // Create Maintenance
      $maint = new MaintenanceLog();
      $maint->machine_id = $request->machine_id;
      $maint->user_id = Auth::user()->id;
      $maint->task = $request->task;
      $maint->save();
      
      // Update Inventories
      $json = json_decode($request->input('json_parts'));
      
      $backstock_location = DB::table('locations')
        ->where('location_name', '=', 'Backstock')
        ->first();
      $backstock_id = $backstock_location->id;
      
      $in_house_location = DB::table('locations')
        ->where('location_name', '=', 'InHouse')
        ->first();
      $inhouse_id = $in_house_location->id;
      
      
      foreach($json as $row)
      {
        $part = DB::table('parts')
          ->where('part_serial', '=', $row->part)
          ->first();
        
        $transfer = new Transfer();
        $transfer->part_id = $part->id;
        $transfer->quantity = $row->quantity;
        $transfer->user_id = Auth::user()->id;
        $transfer->from_location_id = $backstock_id;
        $transfer->to_location_id = $inhouse_id;
       
    
        // Update From Inventory
        DB::table('inventories')
          ->where('part_id', '=', $transfer->part_id)
          ->where('location_id', '=', $transfer->from_location_id)
          ->increment('from_total', $transfer->quantity);

        // Update From Inventory
        DB::table('inventories')
          ->where('part_id', '=', $transfer->part_id)
          ->where('location_id', '=', $transfer->to_location_id)
          ->increment('to_total', $transfer->quantity);

        $transfer->save();
      }
      
      return redirect('maintenance/'.$request->machine_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //DB::enableQueryLog();
        
        $machine = DB::table('machines')
          ->join('printers', 'printers.id', '=', 'machines.printer_id')
          ->join('filaments', 'filaments.id', '=', 'machines.filament_id')
          ->select('printers.name as printer_name', 'filaments.filament_name', 'machines.*')
          ->where('machines.id', '=', $id)
          ->first();
        
        $filaments = DB::table('filaments')
          ->where('active', '=', 1)
          ->get();
      
        $printers = DB::table('printers')
          ->where('active', '=', 1)
          ->get();
      
        $log = DB::table('maintenance_logs as ml')
          ->join('users', 'users.id', '=', 'ml.user_id')
          ->join('machines', 'machines.id', '=', 'ml.machine_id')
          ->join('printers', 'printers.id', '=', 'machines.printer_id')
          ->select(
            'ml.updated_at', 'ml.task', 'ml.updated_at',
            'users.first_name', 'users.last_name',
            'machines.machine_serial', 'machines.id as machine_id',
            'printers.name as printer_name')
          ->where('ml.machine_id', '=', $id)
          ->orderBy('ml.updated_at', 'desc')
          ->get();
      
        if($machine != null)
        {
          if(count($log)>0)
          {
            $machine->last_maintenance = $log[0]->updated_at;
          }
          else
          {
            $machine->last_maintenance = "Never";
          }
        }
      
        //dd(DB::getQueryLog());
      
        return view("pages.maintenance.show")
          ->with('machine', $machine)
          ->with('filaments', $filaments)
          ->with('printers', $printers)
          ->with('log', $log);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
        MaintenanceLog::delete($id);
      
        return redirect()->route('maintenance.index')
          ->with('success', 'Maintenance Deleted.');
      
    }
}
