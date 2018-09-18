<!-- Bags -->
@extends('layouts.app') 
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-2">
    </div>
    <div class="col-8">
      <h3>
        Bags
      </h3>
      @if(count($bags) > 0)
        <table class="table table-sm text-center table-hover">
          <thead>
            <tr>
              <th class="alert-danger" colspan=12>Undelivered Bags</th>
            </tr>
            <tr>
              <th>Created</th>
              <th>Created By</th>
              <th>Part Name</th>
              <th>Part Serial</th>
              <th>Quantity</th>
              <th>Hand Deliver</th>
              <th>Unbag</th>
              <th>Delete</th>
            </tr>
          </thead>
          @foreach($bags as $bag)
            @if($bag->delivered == 0)
              <tbody>

                    <tr>
                      <td>{{date('m-d-Y @ H:i', strtotime($bag->created_at))}}</td>
                      @foreach($users as $user)
                        @if($bag->created_by == $user->id)
                          <td>{{$user->first_name}} {{$user->last_name}}</td>
                        @endif
                      @endforeach
                      <td>{{$bag->part_name}}</td>
                      <td>{{$bag->part_serial}}</td>
                      <td>{{$bag->quantity}}</td>
                      <td><a href="bags/hand_deliver/{{$bag->id}}" class="btn btn-sm btn-outline-success">Hand Deliver</a></td>
                      <td><a href="bags/unbag/{{$bag->id}}" class="btn btn-sm btn-outline-danger">Unbag</a></td>
                      <td><a href="bags/destroy/{{$bag->id}}" class="btn btn-sm btn-outline-danger">Delete</a></td>
                    </tr>
              </tbody>
            @endif
          @endforeach
          
          <thead>
            <tr>
              <th class="alert-success" colspan=12>Delivered Bags</th>
            </tr>
            <tr>
              <th>Created</th>
              <th>Created By</th>
              <th>Delivered On</th>
              <th>Delivered By</th>
              <th>Part Name</th>
              <th>Part Serial</th>
              <th colspan=3>Quantity</th>
            </tr>
          </thead>
          @foreach($bags as $bag)
            @if($bag->delivered == 1)
              <tbody>
                    <tr >
                      <td>{{date('m-d-Y @ H:i', strtotime($bag->created_at))}}</td>
                        @foreach($users as $user)
                          @if($bag->created_by == $user->id)
                            <td>{{$user->first_name}} {{$user->last_name}}</td>
                          @endif
                        @endforeach
                      <td>{{date('m-d-Y @ H:i', strtotime($bag->updated_at))}}</td>
                        @foreach($users as $user)
                          @if($bag->delivered_by == $user->id)
                            <td>{{$user->first_name}} {{$user->last_name}}</td>
                          @endif
                        @endforeach  
                      <td>{{$bag->part_name}}</td>
                      <td>{{$bag->part_serial}}</td>
                      <td colspan=3>{{$bag->quantity}}</td>
                    </tr>
              </tbody>
            @endif
          @endforeach
        </table>
      @else
        There are no bags.
      @endif
    </div>
    <div class="col-2"></div>
  </div>
</div>

@endsection