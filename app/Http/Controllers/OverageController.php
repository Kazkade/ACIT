<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Part;
use App\Overage;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class OverageController extends Controller
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
     
      $overages = DB::table('overages')
        ->join('parts', 'parts.id', '=', 'overages.part_id')
        ->get();
      
      return view('pages.overages.index')
        ->with('overages', $overages);
    }
  
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function resolve($id)
    {
      Overage::where('id', '=', $id)
        ->update(['resolved' => 1]);

      // The creation form is on the sidebar for admins.
      return redirect()->route('overages.index')->with('error', 'Something went wrong.');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unresolve($id)
    {
      // The creation form is on the sidebar for admins.
      return redirect()->route('overages.index')->with('error', 'Unresolving overages should be handled by the database administrator for now.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // The creation form is on the sidebar for admins.
        return redirect()->route('overages.index')->with('error', 'You shouldn\'t be using that route.');
    }
  
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        return redirect()->route('overages.index')->with('error', 'You shouldn\'t be using that route.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return redirect()->route('overages.index')->with('error', 'You shouldn\'t be using that route.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->route('overages.index')->with('error', 'You shouldn\'t be using that route.');
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
        //
        return redirect()->route('overages.index')->with('error', 'You shouldn\'t be using that route.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
        return redirect()->route('overages.index')->with('error', 'You shouldn\'t be using that route.');

    }
  
    
}

