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
              <th>Part Name</th>
              <th>Part Serial</th>
              <th>Created By</th>
              <th>Quantity</th>
              <th>Dismantle</th>
            </tr>
          </thead>
          @foreach($bags as $bag)
            @if($bag->delivered == 0)
              <tbody>

                    <tr>
                      <td>{{$bag->part_name}}</td>
                      <td>{{$bag->part_serial}}</td>
                      @foreach($users as $user)
                        @if($bag->created_by == $user->id)
                          <td>{{$user->first_name}} {{$user->last_name}}</td>
                        @endif
                      @endforeach
                      <td>{{$bag->quantity}}</td>
                      <td><a href="bags/dismantle/{{$bag->id}}" class="btn btn-sm btn-outline-danger">Dismantle</a></td>
                    </tr>
              </tbody>
            @endif
          @endforeach
          
          <thead>
            <tr>
              <th class="alert-success" colspan=12>Delivered Bags</th>
            </tr>
            <tr>
              <th>Part Name</th>
              <th>Part Serial</th>
              <th>Created By</th>
              <th>Delivered By</th>
              <th>Quantity</th>
            </tr>
          </thead>
          @foreach($bags as $bag)
            @if($bag->delivered == 1)
              <tbody>

                    <tr >
                      <td>{{$bag->part_name}}</td>
                      <td>{{$bag->part_serial}}</td>
                      @foreach($users as $user)
                        @if($bag->created_by == $user->id)
                          <td>{{$user->first_name}} {{$user->last_name}}</td>
                        @endif
                      @endforeach
                      @foreach($users as $user)
                        @if($bag->delivered_by == $user->id)
                          <td>{{$user->first_name}} {{$user->last_name}}</td>
                        @endif
                      @endforeach                      
                      <td>{{$bag->quantity}}</td>
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