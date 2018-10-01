<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Machine;
use App\MaintenanceLog;
use DB;

class MachineController extends Controller
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
        $machines = DB::table('machines')
          ->join('printers', 'printers.id', '=', 'machines.printer_id')
          ->join('filaments', 'filaments.id', '=', 'machines.filament_id')
          ->select(
            'machines.*',
            'filaments.filament_name',
            'printers.name as printer_name')
          ->orderBy('machine_serial', 'asc')
          ->get();
      
        //print("<pre>".print_r($machines, true)."</pre>");
      
        foreach($machines as $machine)
        {
          $log = DB::table('maintenance_logs')
            ->where('machine_id', '=', $machine->id)
            ->orderBy('updated_at', 'desc')
            ->first();
          
          $machine->last_maintenance = ($log != null) ? $log->updated_at : "Never";
          
        }
      
        $printers = DB::table('printers')->get();
        $filaments = DB::table('filaments')->get();
      
        //dd(DB::getQueryLog());
      
        return view('pages.machines.index')
          ->with('machines', $machines)
          ->with('printers', $printers)
          ->with('filaments', $filaments);
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

      
        $filament = DB::table('filaments')->where('filament_name', '=', $request->filament_name)->first();
        $printer = DB::table('printers')->where('name', '=', $request->printer_name)->first();
      
        $against = DB::table('machines')
          ->where('machine_serial', '=', $request->machine_serial)
          ->first();

          if($against != null)
          {
            return redirect('machines/')->with('error', "There's already a printer with that machine serial.");
          }
          

      
        DB::table('machines')
          ->insert([
            'machine_serial' => strtoupper($request->machine_serial),
            'identifier' => strtoupper($request->identifier),
            "filament_id" => $filament->id,
            "printer_id" => $printer->id,
          ]);
      
        return redirect('machines/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $filament = DB::table('filaments')->where('filament_name', '=', $request->filament_name)->first();
        $printer = DB::table('printers')->where('name', '=', $request->printer_name)->first();

        DB::table('machines')
          ->where('id', '=', $request->machine_id)
          ->update([
            "machine_serial" => $request->machine_serial,
            "identifier" => $request->identifier,
            "filament_id" => $filament->id,
            "printer_id" => $printer->id,
          ]);
      
        return redirect('maintenance/'.$request->machine_id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ajaxupdate(Request $request)
    {
      $filament = DB::table('filaments')->where('filament_name', '=', $request->filament_name)->first();
      $printer = DB::table('printers')->where('name', '=', $request->printer_name)->first();

      DB::table('machines')
        ->where('id', '=', $request->machine_id)
        ->update([
          "machine_serial" => $request->machine_serial,
          "identifier" => $request->identifier,
          "filament_id" => $filament->id,
          "printer_id" => $printer->id,
        ]);

      return json_encode("Machine updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
        DB::table('machines')->where('id', '=', $id)->delete();
        DB::table('maintenance_logs')->where('machine_id', '=', $id)->delete();
      
        return redirect()->route('machines.index')
          ->with('success', 'Machine deleted. All logs destroyed.');
      
    }
}
