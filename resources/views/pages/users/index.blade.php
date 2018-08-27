@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2"></div>
    <div class="col-9 ">
      <h3>
        Users
      </h3>
      <table class="table table-striped table-sm table-hover text-center align-middle">
        <thead>
          <tr>
            <th scope="col">Last Login</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Active</th>
            <th scope="col">Admin</th>
            <th scope="col">View</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
            <tr>
              <td></td>
              <td>{{$user->first_name}}</td>
              <td>{{$user->last_name}}</td>
              <td>{{$user->username}}</td>
              <td>{{$user->email}}</td>
              <td>
                @if($user->active == 0)
                  <tr class="text-danger">&#10004</tr>
                @else
                  <tr class="text-success">&#10008</tr>
                @endif
              </td>
              <td>
                @if($user->admin == 0)
                  <tr class="text-danger">&#10004</tr>
                @else
                  <tr class="text-success">&#10008</tr>
                @endif
              </td>
              <td><a class="btn btn-outline-dark btn-sm">&#9998</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <span class="p-5"></span>
  </div>
</div>
@endsection