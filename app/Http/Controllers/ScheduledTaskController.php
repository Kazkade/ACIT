<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class ScheduledTaskController extends Controller
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
