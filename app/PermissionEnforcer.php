<?php

namespace App;

use Illuminate\Support\Facades\DB;
use App\Http\Requests;

use Auth;
use DB;

class PermissionEnforcer
{
  
   /* 
    *Copy and paste this in the controller methods you want to block to unauthorized use.
    
    if(AdminEnforcer::Enforce($permission)){
      return redirect()->route('unauthorized');  
    }
    
    * This will enforce that the logged-in user is an admin by default, but any permission key in the database will work.
    */
    public static function Protect($permission = "admin")
    {
      
      if($permission == "admin")
      {
        $data = DB::table('user_permissions')
          ->where('user_id', '=', Auth::user()->id)
          ->where('permission', '=', $permission)
          ->first();
        return ($data->value == 1) ? true : false;
      }
     
    }
}