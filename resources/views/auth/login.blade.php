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
  <div class="row" style="margin-top: 20%;">
    <div class="col-8 offset-2">
      <div class="card " style="vertical-align: middle;">
        <div class="card-header h3">Login</div>
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
          <div class="card-body" >

            {!! csrf_field() !!}

            <div class="mb-3 input-group{{ $errors->has('email') ? ' has-error' : '' }}">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">Email</span>
              </div>
                <input type="text" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"> @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span> @endif
            </div>
            
            <div class="mb-3 input-group{{ $errors->has('password') ? ' has-error' : '' }}">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">Password</span>
              </div>
                <input type="password" class="form-control" placeholder="Password" name="password"> @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span> @endif
              </div>
            </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-outline-primary">
              <i class="fa fa-btn fa-sign-in"></i>Login
            </button>
            <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection