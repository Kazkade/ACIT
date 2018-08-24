<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Part;
use App\Transfer;
use App\Location;
use App\Order;
use App\Inventory;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class OrderController extends Controller
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
     
      $orders = DB::table('orders')
        ->join('parts', 'orders.part_id', '=', 'parts.id')
        ->select('orders.*', 'parts.part_name', 'parts.part_serial', DB::raw('SUM(`orders`.`quantity`) as "total"'))
        ->groupBy('parts.id')
        ->get();
      
      
      $bags = DB::table('bags')
        ->select('*')
        ->where('delivered', '=', 0)
        ->where('marked', '=', 1)
        ->get();
      
      foreach($orders as $order)
      {
        $order->tbd = 0;
        foreach($bags as $bag)
        {
          if($order->part_id == $bag->part_id)
          {
            $order->tbd += $bag->quantity;
          }
        }
        
      }
      
      //die(json_encode($orders));
      
      $users = DB::table('users')
        ->select('id', 'first_name', 'last_name')
        ->get();
      
      foreach($bags as $bag)
      {
        $bag->user_name = '';
        foreach($users as $user)
        {
          if($user->id == $bag->created_by)
          {
            $bag->user_name = $user->first_name." ".$user->last_name;
          }
        }
      }
      
      //die(json_encode($orders));
      
      return view('pages.orders.index')
        ->with('orders', $orders)
        ->with('bags', $bags)
        ->with('users', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // The creation form is on the sidebar for admins.
        return view('pages.orders.index');
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
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      
        return view('pages.orders.show')
          ->with('inventories', $inventories)
          ->with('locations', $locations)
          ->with('part', $part);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('pages.orders.edit')->with('part', $part);
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
        return redirect()->route('orders.index')->with('success', 'Part Created!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
        return redirect()->route('orders.index')->with('success', 'Part '.$part->part_serial.' deleted. '.$deleted_inventories.' were deleted.');

    }
  
    /**
     * Reads the uploaded file and creates entries for each row.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
      
      DB::enableQueryLog();
      
      // Get and Setup File
      if (!$request->hasFile('orders_upload')) 
      {
        throw new \Exception("No file uploaded.");
      }
      
      $file = $request->file('orders_upload');
      
      if (($handle = fopen($file, "r")) === false)
      {
              die("can't open the file.");
      }

      $csv_headers = fgetcsv($handle, 4000, ",");
      $csv_json = array();

      while ($row = fgetcsv($handle, 4000, ","))
      {
              $csv_json[] = array_combine($csv_headers, $row);
      }

      fclose($handle);
      
      // Varables for Response.
      $new_orders = 0;
      $updated_orders = 0;
      $missing_parts = 0;
      $missing_part_array = array("Missing Parts:");
      // Iterations
      foreach($csv_json as $row)
      {
          
        // Look for current order with MO.
        $temp_order = Order::where('mo', $row["name"])->first();

        // Priority Interpreter
        $priority_interpreted = 0; // Green by default.
        
        switch($row["execution_priority_level"])
        {
          case "Red": $priority_interpreted = 2; break;
          case "Yellow": $priority_interpreted = 1; break;
          case "Green": $priority_interpreted = 0; break;
          default: $priority_interpreted = 0; break;
        }

        // If found:
        if(!empty($temp_order))
        {
          $temp_order->quantity = (int)$row["product_qty"];
          $temp_order->created_at = $row["create_date"];
          $temp_order->mo = $row["name"];
          $temp_order->priority = (int)$priority_interpreted;
          $temp_order->part_id = 0;
          // Set "Need new part error" flag to true by default.
          $missing_part_error = 1;
          //Iterate through parts and verify part serial.
          $temp_part_serial = "";
          foreach(DB::table('parts')->get() as $part)
          {
            if(strpos($part->part_serial, $row["product_id/default_code"]) !== false)
            {
              // Get temp part serial.
              $temp_part_serial = $part->part_serial;
              // Force part ID in case there's any changes.
              $temp_order->part_id = (int)$part->id;
              // Turn off "Need new part error" flag.
              $missing_part_error = 0;
            }
          }
          
          // Handle needing new part.
          if($missing_part_error == 1)
          {
            array_push($missing_part_array, $temp_part_serial);
            $missing_parts++;
            continue;
          }
          
          // Save $temp_order.
          $temp_order->save();
          $updated_orders++;
        }
        else
        {

          // Create a new order.
          $new_order = new Order();
          $new_order->mo = $row["name"];
          $new_order->priority = (int)$priority_interpreted;
          $new_order->part_id = 0;
          // Check for missing part.
          $missing_part_error = 1;
          foreach(DB::table('parts')->get() as $part)
          {
            if(strpos($part->part_serial, $row["product_id/default_code"]) !== false)
            {
              // Get temp part serial.
              $temp_part_serial = $part->part_serial;
              // Force part ID in case there's any changes.
              $new_order->part_id = (int)$part->id;
              // Turn off "Need new part error" flag.
              $missing_part_error = 0;
            }
          }
          // If couldn't find the part, continue.
          if($missing_part_error == 1)
          {
            array_push($missing_part_array, $temp_part_serial);
            $missing_parts++;
            continue;
          }
          
          $new_order->quantity = (int)$row["product_qty"];
          $new_order->created_at = $row["create_date"];
          $new_order->save();
          $new_orders++;
        }
        
      }
      echo $new_orders." created.<br>";
      echo $updated_orders." updated.<br>";
      echo $missing_parts." missing.<br>";
      //dd(DB::getQueryLog());
      // Get list of missing serials.
      
      return redirect('/orders')
        ->with('success', $new_orders.' orders created. '.$updated_orders.' orders updated/unchanged. '.$missing_parts.' parts were missing: '.json_encode($missing_part_array));
      //return redirect()->route('orders.index')->('success', 'Orders were updated.');
    }
}

