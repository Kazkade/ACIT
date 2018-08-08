@extends('layouts.app')

@section('content')
<style rel="stylesheet" type="text/css">
  body {
    background: #0f0c29; /* fallback for old browsers */
    background: -webkit-linear-gradient(to right, #0f0c29, #302b63, #24243e); /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to right, #0f0c29, #302b63, #24243e); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
    height: 100%;
    overflow: hidden;
  }
</style>
<div class="container">
    <div class="row" style="margin-top: 10%;">
      <div class="col-8 offset-2">
            <div class="card " style="vertical-align: middle;">
              <div class="card-header h3">Login</div>
              <form class="form-horizontal" id="register_form" role="form" method="POST" action="{{ url('/register') }}">
                <div class="card-body" >
                  {!! csrf_field() !!}

                  <div class="mb-3 input-group{{ $errors->has('name') ? ' has-error' : '' }}">
                      <div class="input-group-prepend">
                        <span class="input-group-text">First & Last Name</span>
                      </div>
                      <input type="text" class="form-control" name="first_name" placeholder="First Name" value="{{ old('first_name') }}">
                      <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}">

                      @if ($errors->has('name'))
                          <span class="help-block">
                              <strong>{{ $errors->first('name') }}</strong>
                          </span>
                      @endif
                  </div>   
                  
                  <div class="mb-3 input-group{{ $errors->has('email') ? ' has-error' : '' }}">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Email</span>
                      </div>
                      <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}">

                      @if ($errors->has('email'))
                          <span class="help-block">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
                      @endif
                  </div>                  

                  <div class="mb-3 input-group{{ $errors->has('password') ? ' has-error' : '' }}">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Password</span>
                      </div>
                      <input type="password" class="form-control" placeholder="Password" name="password">

                      @if ($errors->has('password'))
                          <span class="help-block">
                              <strong>{{ $errors->first('password') }}</strong>
                          </span>
                      @endif
                  </div>

                  <div class="input-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Confirm Password</span>
                      </div>
                      <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation">

                      @if ($errors->has('password_confirmation'))
                          <span class="help-block">
                              <strong>{{ $errors->first('password_confirmation') }}</strong>
                          </span>
                      @endif
                  </div>

                </div>
                <div class="card-footer">
                  <center><div class="g-recaptcha" data-sitekey="6LfwOWUUAAAAAGQWtedgB2cOVxHp2IVLsdIpyM99"></div></center>
                </div>
                <div class="card-footer">
                  <div class="form-group">
                      <div class="col">
                          <button class="btn btn-outline-primary d-block w-100">
                            <i class="fa fa-btn fa-user"></i>Register
                          </button>
                      </div>
                  </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src='https://www.google.com/recaptcha/api.js'></script>
@endsection
