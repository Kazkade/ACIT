@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2"></div>
    <div class="col-8 ">
      <h3>
        Users
      </h3>
      <table class="table table-striped table-sm table-hover text-center align-middle">
        <thead>
          <tr>
            <th scope="col">Last Modified</th>
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
              <td>{{$user->updated_at}}</td>
              <td>{{$user->first_name}}</td>
              <td>{{$user->last_name}}</td>
              <td>{{$user->username}}</td>
              <td>{{$user->email}}</td>
                @if($user->active == 0)
                  <td class="text-danger">&#10004</td>
                @else
                  <td class="text-success">&#10008</td>
                @endif
                @if($user->admin == 0)
                  <td class="text-danger">&#10004</td>
                @else
                  <td class="text-success">&#10008</td>
                @endif
              <td><a class="btn btn-outline-dark btn-sm" href="/user/{{$user->id}}">Stats</a></td>
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