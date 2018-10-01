<?php

namespace App;

use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use Auth;


class PermissionEnforcer
{
  
   /* 
    *Copy and paste this in the routes you want to block to unauthorized use.
    
    // For Classes.
    if(!\App\PermissionEnforcer::Protect()) { return response("Unauthorized", 401); }
    
    // For blade templates.
    @if(\App\PermissionEnforcer::Protect())
      // Do things here if true.
    @endif
    
    Pass in whichever permission key you want to use into the function.
    
    This will enforce that the logged-in user is an admin by default, but any permission key in the database will work.
    */
    public static function Protect($permission = "admin")
    {
      
      if($permission == "admin")
      {
        //print('<pre>'.print_r(\Auth::user(), true).'</pre>');
        return (Auth::user()->admin == 1) ? true : false;
      }
      
      $data = DB::table('user_permissions')
          ->where('user_id', '=', Auth::user()->id)
          ->where('permission', '=', $permission)
          ->first();
      
      if(count($data))
      {
        return ($data->value == 1) ? true : false;
      }
      else
      {
        return response("Permission doesn't exist: ".$permission, 404);
      }
     
    }
}