@extends('layouts.app')

@section('content')

<div class="jumbotron" style="margin-top: 15%;">
  <center>
    <h1 class="display-4">{{$title}}</h1>
    <hr class="my-4">
    @if(Auth::guest())
    You need to be logged in to use this tool.
    <br class="mb-2">
    <a href="/login" class="btn btn-outline-primary btn-primary mr-2">
      Log In
    </a>
    or
    <a href="/register" class="btn btn-outline-primary btn-primary ml-2">
      Register
    </a>
    @else
      @if(Auth::user()->account_type == 0)
        
      @else
        <span class="h3">Welcome Back, {{Auth::user()->first_name}}!</span>
        <br class="mb-2">
        Head to 
        <a href="/dashboard" class="btn btn-outline-primary btn-primary ml-2 mr-2">
          Dashboard
        </a>
        to get started.
      @endif
    @endif
  </center>
</div>

@endsection