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

//use DB; // For using SQL syntax. Try to stick to Eloquent unless it's absolutely necessary.

class UserController extends Controller
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
      
      $users = DB::table('users')
        ->get();
      
      // Return View
      return view('pages.users.index')
        ->with('users', $users);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = DB::table('users')
          ->where('id', '=', $id)
          ->first();
      
        $user_permissions = DB::table('user_permissions')
          ->where('user_id', '=', $id)
          ->get();
        
        $permission_keys = DB::table('permission_keys')
          ->get();
      
        foreach($permission_keys as $key)
        {
          $needs_key = 1;
          foreach($user_permissions as $perm)
          {
            if($perm->permission == $key->key_name)
            {
              $needs_key = 0;
            }
          }
          
          if($needs_key == 1)
          {
            $user_permission = new UserPermission();
            $user_permission->permission = $key->key_name;
            $user_permission->value = $key->default_value;
            $user_permission->user_id = $id;
            $user_permission->save();
          }
        }
        
        return view('pages.users.show')
          ->with('user', $user)
          ->with('permissions', $user_permissions);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function json_update($json)
    {
        $data = json_decode($json);
        
        DB::table('users')
          ->where('id', '=', $data->id)
          ->update([
            "first_name" => $data->first_name,
            "last_name" => $data->last_name,
            "username" => $data->username,
            "email" => $data->email,
            "active" => $data->active,
            "admin" => $data->admin,
          ]);
        
        return json_encode("User updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {    

      User::destroy($id);
      DB::table('user_permissions')
        ->where('id', '=', $id)
        ->delete();
      return redirect()->route('users.index')->with('success', 'Part '.$part->part_serial.' deleted. '.$deleted_inventories.' were deleted.');

    }
}

