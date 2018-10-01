<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Part;
use App\Transfer;
use App\Location;
use App\Inventory;
use App\Bag;
use App\Invite;
use App\User;
use App\UserPermission;

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
          ->orderBy('permission', 'asc')
          ->where('user_id', '=', $id)
          ->get();
        
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
    public function update_permission($user_id, $permission, $value)
    {    
      
      if($permission == "admin")
      {
        DB::table('users')
          ->where('id', '=', $user_id)
          ->update(['admin' => $value]);
        return json_encode(response("User admin priveleges chaned."));
      }
      
      DB::table('user_permissions')
        ->where('user_id', '=', $user_id)
        ->where('permission', '=', $permission)
        ->update(['value' => $value]);
      return json_encode(response("Permission ".$permission." updated to ".$value." for user ".$user_id."."));

    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invite($email)
    {    
        
        $invite = new Invite();
        $invite->email = $email;
        $invite->redeem_token = bin2hex(random_bytes(128));
        $invite->invited_by = Auth::user()->id;
        $invite->expires_at = date('Y-m-d H:i:s', strtotime(time() + (86400 * 7)));
        $invite->save();
      
        $url = "/register?redeem_token=".$invite->redeem_token;
      
        $to = $invite->email;
        $subject = Auth::user()->username." has invited you to ACIT!";
        $message = "You've been invited to create an ACIT account.<br>You can follow the link below to create an account.<br>".config('app.dev_url').$url;
        
        $ret = array(
          "to" => $to, 
          "subject" => $subject, 
          "message" => $message
        );
      
        return $ret;
      
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
        ->where('user_id', '=', $id)
        ->delete();
      return redirect()->route('users.index')->with('success', 'User deleted.');

    }
}

