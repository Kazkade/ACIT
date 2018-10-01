<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Part;
use App\Transfer;
use App\Location;
use App\Inventory;
use App\Bag;
use App\User;
use App\UserPermission;

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class PermissionController extends Controller
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
      // Return View
      return view('pages.permissions.index');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajax()
    { 
      if(!\App\PermissionEnforcer::Protect("perm_keys_index")) { 
        return response("Unauthorized", 401); 
      }
      
      $permissions = DB::table('permission_keys')
        ->get();
      
      // Return View
      return json_encode($permissions);
    }
  
    /**
     * Create a permission on the table and in all users with default value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store($permission, $value, $description)
    {
        if(!\App\PermissionEnforcer::Protect("perm_keys_delete")) { 
          return response("Unauthorized", 401); 
        }
      
        $test = DB::table('permission_keys')
          ->where('key_name', '=', $permission)
          ->first();
      
        if($test != null)
        {
          return json_encode(response('Permisison already exists.'));
        }
      
        DB::table('permission_keys')
          ->insert([
            "key_name" => $permission,
            "default_value" => $value,
            "description" => $description,
          ]);
      
        $users = DB::table('users')
          ->select('id')
          ->get();
      
        foreach($users as $user)
        {
          DB::table('user_permissions')
          ->insert([
            'user_id' => $user->id,
            'permission' => $permission,
            'value' => $value
          ]);
        }
        
        return json_encode(response('Permission created!'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_permission($permission, $value, $description)
    {
        if(!\App\PermissionEnforcer::Protect("perm_keys_modify")) { 
          return response("Unauthorized", 401); 
        }
      
        DB::table('permission_keys')
          ->where('key_name', '=', $permission)
          ->update([
            "default_value" => $value,
            "description" => $description,
          ]);
        
        return json_encode("Permission updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($key)
    {    
      if(!\App\PermissionEnforcer::Protect("perm_keys_delete")) { 
        return response("Unauthorized", 401); 
      }

      DB::table('user_permissions')
        ->where('permission', '=', $key)
        ->delete();
      
      DB::table('permission_keys')
        ->where('key_name', '=', $key)
        ->delete();
      
      return redirect()->route('permissions.index')->with('success', 'User deleted.');

    }
}

