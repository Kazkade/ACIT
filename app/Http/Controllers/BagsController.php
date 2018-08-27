<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Location;
use App\Part;
use App\Transfer;
use App\Inventory;

class BagsController extends Controller
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
    public function mark($id)
    {
        $bag = DB::table('bags')
          ->where('id', '=', $id)
          ->update(['marked' => 1]);
      
        $bag = DB::table('bags')
          ->where('id', '=', $id)
          ->get();
      
        return redirect()->route('deliveries.index', ['part_id='.$bag[0]->part_id]);
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unmark($id)
    {
        $bag = DB::table('bags')
          ->where('id', '=', $id)
          ->update(['marked' => 0]);
      
        $bag = DB::table('bags')
          ->where('id', '=', $id)
          ->get();
      
        return redirect()->route('deliveries.index', ['part_id='.$bag[0]->part_id]);
    }

    
}
