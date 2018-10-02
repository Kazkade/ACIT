<?php

namespace Illuminate\Foundation\Auth;

use DB;
use App\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        return $this->showRegistrationForm();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
      
        if(isset($_GET['redeem_token']))
        {
          $redeemer = DB::table('user_invites')
            ->where('redeem_token', '=', $_GET['redeem_token'])
            ->first();
        }
        else 
        {
          $redeemer = array();
        }
      
        // Check if there is a redeemer.
        if(count($redeemer)) {
          
          // Make sure Timestamp isn't expired.
          if($redeemer->expires_at < time())
          {
            echo "That invite has already expired. Have an administrator issue a new one.";
          }
          
          if (property_exists($this, 'registerView')) {
              return view($this->registerView);
          }

          return view('auth.register');
        }
        else 
        {
          echo '
            <div class="jumbotron">
              <h1 class="display-4"><center>Invite Required:</center></h1>
              <p class="lead"><center>You need to have an invite to register for this software.</center></p>
              <hr class="my-4">
              <p><center>Have an admin create an invite for you and check your email.</center></p>
            </div>
            ';
        }
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        return $this->register($request);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
      
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        Auth::guard($this->getGuard())->login($this->create($request->all()));
        
        // Get last user created.
        $user = DB::table('users')
          ->orderBy('id', 'desc')
          ->first();
        
        // Set Up new permission keys for this user.
        $permission_keys = DB::table('permission_keys')
          ->get();
        
        foreach($permission_keys as $key)
        {
          $perm = new UserPermission();
          $perm->permission = $key->key_name;
          $perm->value = $key->default_value;
          $perm->user_id = $user->id;
          $perm->save();
          echo "Permission ".$key->key_name." at value ".$key->default_value." created for ".$user->id.".<br>";
        }
      
        // Redeem Invite
        DB::table('user_invites')
          ->where('user_id', '=', $user->id)
          ->first()
          ->update([
            'redeemed_at' => date('Y-m-d H:i:s', time())
          ]);
      
        // And mark user as active.
        DB::table('users')
          ->where('id', '=', $user->id)
          ->first([
            'active' => 1
          ]);
        
        return redirect($this->redirectPath());
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return string|null
     */
    protected function getGuard()
    {
        return property_exists($this, 'guard') ? $this->guard : null;
    }
}
