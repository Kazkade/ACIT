<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Part;
use App\Transfer;
use App\Location;
use App\Order;
use App\Overage;
use App\Delivery;
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
        ->where('closed', '=', 0)
        ->get();
      
      $closed = DB::table('orders')
        ->join('parts', 'parts.id', '=', 'orders.part_id')
        ->select('orders.id', 'orders.mo', 'orders.updated_at', 'orders.quantity', 'parts.part_serial', 'parts.part_name', 'orders.part_id', 'orders.priority')
        ->orderBy('mo', 'asc')
        ->where('closed', '=', 1)
        ->get();
      
      $mos = DB::table('orders')
        ->select('orders.id', 'orders.mo', 'orders.quantity', 'orders.part_id', 'orders.priority')
        ->where('closed', '=', 0)
        ->orderBy('mo', 'asc')
        ->get();
      
      $bags = DB::table('bags')
        ->select('*')
        ->where('delivered', '=', 0)
        ->where('marked', '=', 1)
        ->get();
      
      // For Open Orders
      if(count($orders) > 0)
      {
        foreach($orders as $order)
        {
          $order->tbd = 0;
          $order->mos = array();
          foreach($bags as $bag)
          {
            if($order->part_id == $bag->part_id)
            {
              $order->tbd += $bag->quantity;
            }
          }
          foreach($mos as $mo)
          {
            if($mo->part_id == $order->part_id)
            {
              array_push($order->mos, $mo);
            }
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
        ->with('closed', $closed)
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deliver() 
    {
      
      DB::enableQueryLog();
      
      //$bags = DB::raw('SELECT *, SUM(`quantity`) as "total" FROM `bags` WHERE `marked` = 1 GROUP BY `part_id`')
        
      $bags = DB::table('bags')
        ->select(DB::raw('id, part_id, SUM(`quantity`) as "total"'))
        ->where('marked', '=', 1)
        ->where('delivered', '=', 0)
        ->groupBy('part_id')
        ->get();
      
      $orders = DB::table('orders')
        ->where('quantity','>','filled')
        ->get();
      /*
      Get list of bags with total quantities marked for delivery 
        SELECT *, SUM(`quantity`) as "total" FROM `bags` WHERE `marked` = 1 GROUP BY `part_id`
      Loop through bags, then orders by MO asc, 
        SELECT * FROM `orders`WHERE `filled` < `quantity` order by `mo` asc 
      Apply to orders as they appear with the same part id, decrementing "total" from bags
        ** Check if bag can fill fulll order, first.
      After the loop, if "total" is still greater than 0, create an overage.
      */
      // Create Delivery for reference.
      $delivery = new Delivery();
      $delivery->user_id = Auth::user()->id;
      $delivery->save();
      $delivery = DB::table('deliveries')
        ->orderBy('updated_at', 'desc')
        ->first();
      
      foreach($bags as $bag)
      {
        foreach($orders as $order)
        {
          if($bag->part_id == $order->part_id)
          {
            if($order->quantity - $order->filled > $bag->total)
            {
              $order->filled += $bag->total;
              Order::where('id', $order->id)
                ->update(['filled' => $bag->total]);
            }
            else
            {
              $bag->total -= $order->quantity - $order->filled;
              $order->filled = $order->quantity;
              
              Order::where('id', $order->id)
                ->update(['filled' => $order->quantity, 'closed' => 1]);
              
            }
          }
        }
        // Overages
        if($bag->total > 0)
        {
          $overage = new Overage();
          $overage->part_id = $bag->part_id;
          $overage->quantity = $bag->total;
          $overage->delivery_id = $delivery->id;
          $overage->save();
          //echo "<br> Overage for ".$bag->total." for part with ID ".$bag->part_id." was created.";
        }
      }
      
      //dd(DB::getQueryLog());
      
      // Mark All Bags as Delivered with details.
      $bags = DB::table('bags')
        ->where('marked', '=', 1)
        ->where('delivered', '=', 0)
        ->update([
          'delivered' => 1,
          'delivery_id' => $delivery->id,
          'delivered_by' => Auth:: user()->id
        ]);   
      
      return redirect('/deliveries/'.$delivery->id);

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
        $this->validate($request, [
          'mo' => 'required',
          'part_serial' => 'required',
          'quantity' => 'required',
          'priority' => 'required',
        ]);  
      
        // Check against current MOs
        $order = DB::table('orders')
          ->where('mo', '=', "MO/".$request->input('mo'))
          ->first();
      
        if($order != null)
        {
          return redirect()->route('orders.index')->with('error', 'That MO already exists.');
        }
      
        // Get Part
        $part = DB::table('parts')
          ->where('part_serial', '=', $request->input('part_serial'))
          ->first();
      
        // Check if part exists.
        if($part == null)
        {
          return redirect()->route('orders.index')->with('error', 'That part doesn\'t exist.');
        }
      
        $order = new Order();
        $order->mo = "MO/".$request->input('mo');
        $order->quantity = (int)$request->input("quantity");
        $order->priority = $request->input('priority');
        $order->part_id = $part->id;
        $order->save();
      
        return redirect()->route('orders.index')->with('success', "Order Created!");
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    
      
      $order = DB::table('orders')
        ->join('parts', 'parts.id', '=', 'orders.part_id')
        ->select('parts.part_name', 'orders.*')
        ->where('orders.id', '=', $id)
        ->first();
      
      Order::destroy($id);
      
      return redirect()->route('orders.index')->with('success', 'Order '.$order->mo.' for '.$order->quantity.' '.$order->part_name.' was deleted.');

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
        $temp_order = Order::where('mo', $row["Reference"])->first();

        // Priority Interpreter
        $priority_interpreted = 0; // Green by default.
        
        switch($row["Buffer On-Hand Alert Level"])
        {
          case "Red": $priority_interpreted = 2; break;
          case "Yellow": $priority_interpreted = 1; break;
          case "Green": $priority_interpreted = 0; break;
          default: $priority_interpreted = 0; break;
        }

        // If found:
        if(!empty($temp_order))
        {
          $temp_order->quantity = (int)$row["Product Quantity"];
          $temp_order->created_at = $row["Created on"];
          $temp_order->mo = $row["Reference"];
          $temp_order->priority = (int)$priority_interpreted;
          $temp_order->part_id = 0;
          // Set "Need new part error" flag to true by default.
          $missing_part_error = 1;
          //Iterate through parts and verify part serial.
          $temp_part_serial = "";
          foreach(DB::table('parts')->get() as $part)
          {
            if(strpos($row["Product/Internal Reference"], $part->part_serial) !== false)
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
            array_push($missing_part_array, $row["Product/Internal Reference"]);
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
          $new_order->mo = $row["Reference"];
          $new_order->priority = (int)$priority_interpreted;
          $new_order->part_id = 0;
          // Check for missing part.
          $missing_part_error = 1;
          foreach(DB::table('parts')->get() as $part)
          {
            if(strpos($part->part_serial, $row["Product/Internal Reference"]) !== false)
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
          
          $new_order->quantity = (int)$row["Product Quantity"];
          $new_order->created_at = $row["Created on"];
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

